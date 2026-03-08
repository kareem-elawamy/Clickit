<?php

namespace App\Utils;

use App\Models\ShippingMethod;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Shop;
use App\Models\User;
use App\Models\Admin;
use App\Models\Color;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Storage;
use App\Models\AdminWallet;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Traits\CommonTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CartShipping;
use App\Models\SellerWallet;
use App\Models\ShippingType;
use App\Traits\PdfGenerator;
use App\Traits\CustomerTrait;
use App\Models\BusinessSetting;
use App\Models\OfflinePayments;
use App\Models\ShippingAddress;
use App\Events\OrderPlacedEvent;
use App\Models\OrderTransaction;
use App\Models\ReferralCustomer;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\DigitalProductVariation;


class OrderManager
{
    use CommonTrait, PdfGenerator;
    use CustomerTrait;

    public static function generateUniqueOrderID(): string
    {
        return rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
    }

    public static function getOrderSummaryBeforePlaceOrder($cart, $coupon_discount): array
    {
        $coupon_code = session()->has('coupon_code') ? session('coupon_code') : 0;
        $coupon = Coupon::where(['code' => $coupon_code])->where('status', 1)->first();

        $subTotal = 0;
        $totalDiscountOnProduct = 0;

        if ($coupon && ($coupon->seller_id == NULL || $coupon->seller_id == '0' || $coupon->seller_id == $cart[0]->seller_id)) {
            $coupon_discount = $coupon->coupon_type == 'free_delivery' ? 0 : $coupon_discount;
        } else {
            $coupon_discount = 0;
        }

        foreach ($cart as $item) {
            $subTotal += $item->price * $item->quantity;
            $totalDiscountOnProduct += $item->discount * $item->quantity;
        }

        $orderTotal = $subTotal - $totalDiscountOnProduct - $coupon_discount;
        return [
            'order_total' => $orderTotal
        ];
    }

    public static function getStockUpdateOnOrderStatusChange($order, $status): void
    {
        if ($status == 'returned' || $status == 'failed' || $status == 'canceled') {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = Product::find($detail['product_id']);
                    $type = $detail['variant'];
                    $variationData = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] += $detail['qty'];
                        }
                        $variationData[] = $var;
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($variationData),
                        'current_stock' => $product['current_stock'] + $detail['qty'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0,
                        'delivery_status' => $status
                    ]);
                }
            }
        } else {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $product = Product::find($detail['product_id']);

                    $type = $detail['variant'];
                    $variationData = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] -= $detail['qty'];
                        }
                        $variationData[] = $var;
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($variationData),
                        'current_stock' => $product['current_stock'] - $detail['qty'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 1,
                        'delivery_status' => $status
                    ]);
                }
            }
        }
    }

    public static function getWalletManageOnOrderStatusChange($order, $received_by): void
    {
        $order = Order::find($order['id']);
        $order_summary = OrderManager::getOrderTotalAndSubTotalAmountSummary($order);
        $order_amount = $order_summary['subtotal'] - $order_summary['total_discount_on_product'] - $order['discount_amount'];
        $commission = $order['admin_commission'];
        $shipping_model = $order->shipping_responsibility;

        OrderManager::getCheckOrCreateAdminWallet();

        if (!SellerWallet::where('seller_id', $order['seller_id'])->first()) {
            DB::table('seller_wallets')->insert([
                'seller_id' => $order['seller_id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($order->coupon_code && $order->coupon_code != '0' && $order->seller_is == 'seller' && $order->discount_type == 'coupon_discount') {
            if ($order->coupon_discount_bearer == 'inhouse') {
                $seller_wallet = SellerWallet::where('seller_id', $order->seller_id)->first();
                $seller_wallet->total_earning += $order->discount_amount;
                $seller_wallet->save();

                $paid_by = 'admin';
                $payer_id = 1;
                $payment_receiver_id = $order->seller_id;
                $paid_to = 'seller';
            } elseif ($order->coupon_discount_bearer == 'seller') {
                $paid_by = 'seller';
                $payer_id = $order->seller_id;
                $payment_receiver_id = $order->seller_id;
                $paid_to = 'admin';
            }

            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->payment_for = 'coupon_discount';
            $transaction->payer_id = $payer_id;
            $transaction->payment_receiver_id = $payment_receiver_id;
            $transaction->paid_by = $paid_by;
            $transaction->paid_to = $paid_to;
            $transaction->payment_status = 'disburse';
            $transaction->amount = $order->discount_amount;
            $transaction->transaction_type = 'expense';
            $transaction->save();
        }

        // free delivery over amount transaction start
        if ($order->is_shipping_free && $order->seller_is == 'seller') {

            $seller_wallet = SellerWallet::where('seller_id', $order->seller_id)->first();
            $admin_wallet = AdminWallet::where('admin_id', 1)->first();

            if ($order->free_delivery_bearer == 'admin' && $order->shipping_responsibility == 'sellerwise_shipping') {
                $seller_wallet->delivery_charge_earned += $order->extra_discount;
                $seller_wallet->total_earning += $order->extra_discount;

                $admin_wallet->delivery_charge_earned -= $order->extra_discount;
                $admin_wallet->inhouse_earning -= $order->extra_discount;

                $paid_by = 'admin';
                $payer_id = 1;
                $payment_receiver_id = $order->seller_id;
                $paid_to = 'seller';
            } elseif ($order->free_delivery_bearer == 'seller' && $order->shipping_responsibility == 'inhouse_shipping') {
                $seller_wallet->delivery_charge_earned -= $order->extra_discount;
                $seller_wallet->total_earning -= $order->extra_discount;

                $admin_wallet->delivery_charge_earned += $order->extra_discount;
                $admin_wallet->inhouse_earning += $order->extra_discount;

                $paid_by = 'seller';
                $payer_id = $order->seller_id;
                $payment_receiver_id = $order->seller_id;
                $paid_to = 'admin';
            } elseif ($order->free_delivery_bearer == 'seller' && $order->shipping_responsibility == 'sellerwise_shipping') {
                $paid_by = 'seller';
                $payer_id = $order->seller_id;
                $payment_receiver_id = $order->seller_id;
                $paid_to = 'seller';
            } elseif ($order->free_delivery_bearer == 'admin' && $order->shipping_responsibility == 'inhouse_shipping') {
                $paid_by = 'admin';
                $payer_id = 1;
                $payment_receiver_id = $order->seller_id;
                $paid_to = 'admin';
            }

            $seller_wallet->save();
            $admin_wallet->save();

            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->payment_for = 'free_shipping_over_order_amount';
            $transaction->payer_id = $payer_id;
            $transaction->payment_receiver_id = $payment_receiver_id;
            $transaction->paid_by = $paid_by;
            $transaction->paid_to = $paid_to;
            $transaction->payment_status = 'disburse';
            $transaction->amount = $order->extra_discount;
            $transaction->transaction_type = 'expense';
            $transaction->save();
        }
        // free delivery over amount transaction end


        if ($order['payment_method'] == 'cash_on_delivery' || $order['payment_method'] == 'offline_payment') {
            DB::table('order_transactions')->insert([
                'transaction_id' => OrderManager::generateUniqueOrderID(),
                'customer_id' => $order['customer_id'],
                'seller_id' => $order['seller_id'],
                'seller_is' => $order['seller_is'],
                'order_id' => $order['id'],
                'order_amount' => $order_amount,
                'seller_amount' => $order_amount - $commission,
                'admin_commission' => $commission,
                'received_by' => $received_by,
                'status' => 'disburse',
                'delivery_charge' => $order['shipping_cost'] - ($order['is_shipping_free'] ? $order['extra_discount'] : 0),
                'tax' => $order_summary['total_tax'],
                'delivered_by' => $received_by,
                'payment_method' => $order['payment_method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            ($shipping_model == 'inhouse_shipping' && !$order['is_shipping_free']) ? $wallet->delivery_charge_earned += $order['shipping_cost'] : null;
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                ($shipping_model == 'sellerwise_shipping' && !$order['is_shipping_free']) ? $wallet->delivery_charge_earned += $order['shipping_cost'] : null;

                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;
                $wallet->total_tax_collected += $order_summary['total_tax'];

                if ($shipping_model == 'sellerwise_shipping') {
                    !$order['is_shipping_free'] ? $wallet->delivery_charge_earned += $order['shipping_cost'] : null;
                    $wallet->collected_cash += $order['order_amount']; //total order amount
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->save();
            }
        } else {
            $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
            if ($transaction) {
                $transaction->status = 'disburse';
                $transaction->save();
            }

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            $wallet->pending_amount -= $order['order_amount'];
            ($shipping_model == 'inhouse_shipping' && !$order['is_shipping_free']) ? $wallet->delivery_charge_earned += $order['shipping_cost'] : null;
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                ($shipping_model == 'sellerwise_shipping' && !$order['is_shipping_free']) ? $wallet->delivery_charge_earned += $order['shipping_cost'] : null;
                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;

                if ($shipping_model == 'sellerwise_shipping') {
                    !$order['is_shipping_free'] ? $wallet->delivery_charge_earned += $order['shipping_cost'] : null;
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'] + $order['shipping_cost'];
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            }
        }
    }

    public static function getOrderAddressId(string|null $type = 'shipping_address', int|null $id = null): int|null
    {
        $addressId = 0;
        if ($type == 'shipping_address') {
            $addressId = session('address_id') ? session('address_id') : 0;
            if (!is_null($id) && !session()->has('address_id')) {
                $addressId = $id;
            }
        }

        if ($type == 'billing_address') {
            $addressId = session('billing_address_id') ? session('billing_address_id') : 0;
            if (!is_null($id) && !session()->has('billing_address_id')) {
                $addressId = $id;
            }
            $addressId = getWebConfig('billing_input_by_customer') ? $addressId : null;
        }
        return $addressId;
    }

    public static function updateCustomerShippingAddressForOrder($guestID, $customerID, $addressId): void
    {
        ShippingAddress::where(['customer_id' => $guestID, 'is_guest' => 1, 'id' => $addressId])
            ->update(['customer_id' => $customerID, 'is_guest' => 0]);
    }

    public static function getTotalCouponAmount($request, $couponCode): array
    {
        $user = Helpers::getCustomerInformation($request);
        if ($user == 'offline') {
            return [
                'status' => false,
                'messages' => translate('Coupon_not_aplicable'),
            ];
        }
        $couponLimit = Order::where(['customer_id' => $user['id'], 'coupon_code' => $couponCode])
            ->groupBy('order_group_id')->get()->count();

        $firstCoupon = Coupon::where(['code' => $couponCode])
            ->where('status', 1)
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))->first();

        if (!$firstCoupon) {
            return [
                'status' => false,
                'messages' => translate('invalid_coupon')
            ];
        }

        $coupon = $firstCoupon['coupon_type'] == 'first_order' ? $firstCoupon : ($firstCoupon['limit'] > $couponLimit ? $firstCoupon : null);

        if ($coupon && $coupon['coupon_type'] == 'first_order') {
            if (Order::where(['customer_id' => $user['id']])->count() > 0) {
                return [
                    'status' => false,
                    'messages' => translate('sorry_this_coupon_is_not_valid_for_this_user') . '!',
                ];
            }
        }

        $cartList = CartManager::getCartListQueryAPI(request: $request, type: 'checked');
        if (count($cartList) <= 0) {
            return [
                'status' => false,
                'messages' => translate('Please_add_item_to_cart')
            ];
        }

        $couponType = $coupon->coupon_type;
        $couponDiscountType = $coupon->discount_type;
        $couponDiscount = $coupon->discount;

        if ($coupon->coupon_type != 'first_order' && $coupon['customer_id'] != 0 && $coupon['customer_id'] != $user['id']) {
            return [
                'status' => false,
                'messages' => translate('coupon_not_valid')
            ];
        }

        $onlyProductTotalAmount = 0;
        if ($coupon->coupon_type == 'first_order') {
            foreach ($cartList as $cartItem) {
                $onlyProductTotalAmount += ($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity'];
            }
        }

        if ($coupon->coupon_type == 'discount_on_purchase') {
            foreach ($cartList as $cartItem) {
                if (($coupon->seller_id == '0') || (is_null($coupon->seller_id) && $cartItem['seller_is'] == 'admin') || ($coupon->seller_id == $cartItem['seller_id'] && $cartItem['seller_is'] == 'seller')) {
                    $onlyProductTotalAmount += ($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity'];
                }
            }
        }

        $discount = 0;
        if ($coupon->coupon_type == 'first_order' || $coupon->coupon_type == 'discount_on_purchase') {
            if ($onlyProductTotalAmount <= 0 || $couponDiscount > $onlyProductTotalAmount || $coupon['min_purchase'] > $onlyProductTotalAmount) {
                return [
                    'status' => false,
                    'messages' => translate('minimum_purchase')
                ];
            }

            if ($couponDiscountType == 'percentage') {
                $discountAmount = ($onlyProductTotalAmount * $couponDiscount) / 100;
                $discount = $discountAmount > $coupon['max_discount'] ? $coupon['max_discount'] : $discountAmount;
            } else {
                $discount = $couponDiscount;
            }
        } elseif ($coupon->coupon_type == 'free_delivery') {
            foreach ($cartList as $cartItem) {
                if (($coupon->seller_id == '0') || (is_null($coupon->seller_id) && $cartItem['seller_is'] == 'admin') || ($coupon->seller_id == $cartItem['seller_id'] && $cartItem['seller_is'] == 'seller')) {
                    $onlyProductTotalAmount += ($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity'];
                }
            }
            foreach ($cartList->groupBy('cart_group_id') as $cartGroupItem) {
                $cartItem = $cartGroupItem->first();
                if ($cartItem) {
                    if (($coupon->seller_id == '0') || (is_null($coupon->seller_id) && $cartItem['seller_is'] == 'admin') || ($coupon->seller_id == $cartItem['seller_id'] && $cartItem['seller_is'] == 'seller')) {
                        $discount += \App\Utils\CartManager::get_shipping_cost(groupId: $cartItem['cart_group_id'], type: 'checked');
                    }
                }
            }
        }

        if ($discount > 0) {
            session()->put('coupon_code', $coupon['code']);
            session()->put('coupon_type', $couponType);
            session()->put('coupon_discount', $discount);
            session()->put('coupon_bearer', $coupon->coupon_bearer);
            session()->put('coupon_seller_id', $coupon->seller_id);

            return [
                'status' => true,
                'discount' => $discount,
                'coupon_type' => $couponType,
                'total_cart_amount' => $onlyProductTotalAmount,
                'messages' => translate('coupon_applied_successfully') . '!'
            ];
        }

        return [
            'status' => false,
            'messages' => translate('coupon_not_valid')
        ];
    }

    public static function getGroupWiseCouponArray($request, $couponCode, $applicableAmount, $discountAmount, $groupId, $cartListGroup): array
    {
        $user = Helpers::getCustomerInformation($request);
        if ($applicableAmount <= 0 || $discountAmount <= 0 || $user == 'offline') {
            return [
                'discount' => 0,
                'coupon_bearer' => 'inhouse',
                'coupon_code' => 0,
                'coupon_type' => NULL,
            ];
        }

        $couponLimit = Order::where(['customer_id' => $user['id'], 'coupon_code' => $couponCode])
            ->groupBy('order_group_id')->get()->count();

        $firstCoupon = Coupon::where(['code' => $couponCode])
            ->where('status', 1)
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))->first();

        $coupon = $firstCoupon && $firstCoupon['coupon_type'] == 'first_order' ? $firstCoupon : ($firstCoupon['limit'] > $couponLimit ? $firstCoupon : null);

        $firstOrderCoupon = $coupon->coupon_type == 'first_order';
        $discountOnPurchaseCoupon = $coupon->coupon_type == 'discount_on_purchase';
        $freeDeliveryCoupon = $coupon->coupon_type == 'free_delivery';

        $onlyProductTotalAmount = 0;
        if ($firstOrderCoupon) {
            foreach ($cartListGroup as $cartListGroupKey => $cartList) {
                if ($cartListGroupKey == $groupId) {
                    foreach ($cartList as $cartItem) {
                        $onlyProductTotalAmount += ($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity'];
                    }
                }
            }
        }

        if ($discountOnPurchaseCoupon) {
            foreach ($cartListGroup as $cartListGroupKey => $cartList) {
                if ($cartListGroupKey == $groupId) {
                    foreach ($cartList as $cartItem) {
                        if (($coupon->seller_id == '0') || (is_null($coupon->seller_id) && $cartItem['seller_is'] == 'admin') || ($coupon->seller_id == $cartItem['seller_id'] && $cartItem['seller_is'] == 'seller')) {
                            $onlyProductTotalAmount += ($cartItem['price'] - $cartItem['discount']) * $cartItem['quantity'];
                        }
                    }
                }
            }
        }

        $discount = 0;
        if ($firstOrderCoupon || $discountOnPurchaseCoupon) {
            $discount = ($onlyProductTotalAmount * $discountAmount) / $applicableAmount;
        } elseif ($freeDeliveryCoupon) {
            foreach ($cartListGroup as $cartListGroupKey => $cartList) {
                if ($cartListGroupKey == $groupId) {
                    $cartItem = $cartList->first();
                    if ($cartItem) {
                        if (($coupon->seller_id == '0') || (is_null($coupon->seller_id) && $cartItem['seller_is'] == 'admin') || ($coupon->seller_id == $cartItem['seller_id'] && $cartItem['seller_is'] == 'seller')) {
                            $discount += \App\Utils\CartManager::get_shipping_cost(groupId: $groupId, type: 'checked');
                        }
                    }
                }
            }
        }

        return [
            'discount' => $discount,
            'coupon_bearer' => $coupon->coupon_bearer,
            'coupon_code' => $coupon->code,
            'coupon_type' => $coupon->coupon_type,
        ];
    }

    public static function getCustomerInfoForOrder($data): array
    {
        $customer = Helpers::getCustomerInformation($data['requestObj'] ?? request()->all());
        $isGuest = ($customer == 'offline') ? 1 : 0;
        if (isset($data['is_guest'])) {
            $isGuest = $data['is_guest'] ?? 0;
        }

        if ($customer == 'offline' && isset($data['customer_id']) && isset($data['is_guest']) && $data['is_guest'] == 0) {
            $userCheck = User::where(['id' => $data['customer_id']])->first();
            $customer = $userCheck ?? $customer;
        }

        $guestId = session('guest_id') ? session('guest_id') : ($data['is_guest'] ? $data['guest_id'] : 0);
        $customerId = $customer == 'offline' ? $guestId : $customer['id'];

        if ($customer == 'offline' && session('newCustomerRegister') && session('newRegisterCustomerInfo')) {
            $addCustomer = session('newRegisterCustomerInfo');
            $customerId = $addCustomer['id'];
            $isGuest = 0;
            $guestID = session()->has('guest_id') ? session('guest_id') : 0;

            self::updateCustomerShippingAddressForOrder($guestID, $customerId, session('address_id'));
            self::updateCustomerShippingAddressForOrder($guestID, $customerId, session('billing_address_id'));
        } elseif ($customer == 'offline' && isset($data['newCustomerRegister'])) {
            $customerId = $data['newCustomerRegister'] ? $data['newCustomerRegister']['id'] : $isGuest;
            $isGuest = $data['newCustomerRegister'] ? 0 : 1;

            if (isset($data['is_guest']) && isset($data['guest_id']) && isset($data['address_id']) && isset($data['billing_address_id'])) {
                self::updateCustomerShippingAddressForOrder($data['guest_id'], $customerId, $data['address_id']);
                self::updateCustomerShippingAddressForOrder($data['guest_id'], $customerId, $data['billing_address_id']);
            }
        } elseif ($customer == 'offline' && isset($data['new_customer_id'])) {
            $customerId = $data['new_customer_id'];
            $isGuest = $data['new_customer_id'] ? 0 : 1;
        }

        $newCustomerRegister = isset($data['newCustomerRegister']) && empty($data['newCustomerRegister']) ? $data['newCustomerRegister'] : session('newRegisterCustomerInfo');

        return [
            'is_guest' => $isGuest,
            'guest_id' => $guestId,
            'customer' => $customer,
            'customer_id' => $customerId,
            'newCustomerRegister' => $newCustomerRegister,
            'new_customer_id' => $newCustomerRegister ? $newCustomerRegister['id'] : null,
        ];
    }

    public static function generateNewOrderID()
    {
        $baseID = 100001;
        $orderDetailsId = OrderDetail::orderBy('order_id', 'desc')->first()?->order_id ?? $baseID;
        if (Order::find($orderDetailsId)) {
            $orderDetailsId = Order::orderBy('id', 'DESC')->first()->id + 1;
        }
        return $orderDetailsId;
    }

    public static function getCheckOrCreateAdminWallet(): void
    {
        if (!AdminWallet::where('admin_id', 1)->first()) {
            DB::table('admin_wallets')->insert([
                'admin_id' => 1,
                'withdrawn' => 0,
                'commission_earned' => 0,
                'inhouse_earning' => 0,
                'delivery_charge_earned' => 0,
                'pending_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public static function checkCustomerReferralDiscount(object|array|null $request = null)
    {
        $user = Helpers::getCustomerInformation($request);
        if ($user != 'offline') {
            $referralCustomer = ReferralCustomer::where('user_id', $user['id'])->where('is_used', 0)->first();
            $isFirstOrder = Order::where(['customer_id' => $user['id'], 'order_status' => 'delivered', 'payment_status' => 'paid', 'order_type' => 'default_type'])->count();
            return $referralCustomer && $isFirstOrder <= 0 ? $referralCustomer : null;
        }
        return null;
    }

    public static function redeemReferralDiscount($referralCustomer, $totalAmount, $groupAmount): float|int
    {
        if ($referralCustomer) {
            $discountAmount = $referralCustomer->customer_discount_amount;
            $validity = $referralCustomer->customer_discount_validity;
            $type = $referralCustomer->customer_discount_validity_type;

            $expirationDate = Carbon::parse($referralCustomer->created_at);
            if ($type == 'day') {
                $expirationDate->addDays($validity);
            } else if ($type == 'week') {
                $expirationDate->addWeeks($validity);
            } else if ($type == 'month') {
                $expirationDate->addMonths($validity);
            } else {
                return 0;
            }

            if (Carbon::now()->greaterThan($expirationDate)) {
                return 0;
            }

            if ($referralCustomer->customer_discount_amount_type === 'percentage') {
                return ($groupAmount * $discountAmount) / 100;
            }
            return ($discountAmount * $groupAmount) / $totalAmount;
        }
        return 0;
    }

    public static function generateReferBonusForFirstOrder(int|string $orderId): void
    {
        $refEarningStatus = getWebConfig(name: 'ref_earning_status') ?? 0;
        $refEarningExchangeRate = getWebConfig(name: 'ref_earning_exchange_rate') ?? 0;
        $order = Order::with(['customer', 'seller.shop', 'deliveryMan'])->where(['id' => $orderId])->first();

        if (!$order['is_guest'] && $refEarningStatus == 1 && $order['order_status'] == 'delivered') {
            $customer = User::where(['id' => $order['customer_id']])->first();
            $isFirstOrder = Order::where(['customer_id' => $order['customer_id'], 'order_status' => 'delivered', 'payment_status' => 'paid'])->count();
            $referredByUser = User::where(['id' => $customer['referred_by']])->first();
            if ($isFirstOrder == 1 && isset($customer->referred_by) && isset($referredByUser)) {
                self::createWalletTransaction(
                    user_id: $referredByUser['id'],
                    amount: usdToDefaultCurrency(amount: $refEarningExchangeRate),
                    transaction_type: 'add_fund_by_admin',
                    reference: 'earned_by_referral'
                );
            }
        }
    }

    public static function processOrderGenerateData(array|object|null $data = []): array
    {
        $cartListQuery = CartManager::getCartListQuery(type: 'checked');
        $cartListQueryGroup = $cartListQuery?->groupBy('cart_group_id');
        $checkReferralDiscount = OrderManager::checkCustomerReferralDiscount(request: ($data['requestObj'] ?? request()->all()));
        $onlyProductPriceGrandTotal = CartManager::getOnlyCartProductPriceGrandTotal(type: 'checked');

        $shippingModel = getWebConfig(name: 'shipping_method');
        $adminShipping = ShippingType::where('seller_id', 0)->first();

        $couponCode = $data['coupon_code'] ?? session('coupon_code');
        $totalCouponDiscountInfo = OrderManager::getTotalCouponAmount(request: ($data['requestObj'] ?? request()->all()), couponCode: $couponCode);
        $totalCouponDiscount = $totalCouponDiscountInfo['discount'] ?? 0;

        $vendorWiseCartList = [];
        foreach ($cartListQueryGroup as $groupId => $cartListQueryGroupItem) {

            $couponInfo = OrderManager::getGroupWiseCouponArray(
                request: ($data['requestObj'] ?? request()->all()),
                couponCode: $couponCode,
                applicableAmount: $totalCouponDiscountInfo['total_cart_amount'] ?? 0,
                discountAmount: $totalCouponDiscountInfo['discount'] ?? 0,
                groupId: $groupId,
                cartListGroup: $cartListQueryGroup
            );
            $shippingCost = CartManager::get_shipping_cost(groupId: $groupId, type: 'checked');
            $freeDelivery = OrderManager::getFreeDeliveryOrderAmountArray($groupId);
            $isShippingFree = 0;
            $freeShippingDiscount = 0;
            $freeDeliveryBearer = null;
            $extraDiscountType = null;
            if ($freeDelivery['status'] && $freeDelivery['shipping_cost_saved'] > 0 && $couponInfo['coupon_type'] != 'free_delivery') {
                $isShippingFree = 1;
                $extraDiscountType = 'free_shipping_over_order_amount';
                $freeShippingDiscount = $shippingCost;
                $freeDeliveryBearer = $freeDelivery['responsibility'];
            }

            $firstCartItem = $cartListQueryGroupItem->first();
            $cartGroupGrandTotal = CartManager::cart_grand_total(cartGroupId: $groupId, type: 'checked');
            $onlyGroupPriceGrandTotal = CartManager::getOnlyCartProductPriceGrandTotal(cartGroupId: $groupId, type: 'checked');

            if ($shippingModel == 'inhouse_shipping' || $firstCartItem['seller_is'] == 'admin') {
                $shippingType = $adminShipping;
            } else if ($firstCartItem['seller_is'] != 'admin') {
                $shippingType = ShippingType::where(['seller_id' => $firstCartItem['seller_id']])->first();
            }

            $shippingMethod = CartShipping::where(['cart_group_id' => $groupId])->first();
            $shippingMethodId = isset($shippingMethod) ? $shippingMethod->shipping_method_id : 0;
            $cartGroupOrderAmount = $cartGroupGrandTotal - ($couponInfo['discount'] ?? 0) - $freeShippingDiscount;

            $referAndEarnDiscount = OrderManager::redeemReferralDiscount(
                referralCustomer: $checkReferralDiscount,
                totalAmount: $onlyProductPriceGrandTotal - $totalCouponDiscount,
                groupAmount: $onlyGroupPriceGrandTotal - ($couponInfo['discount'] ?? 0),
            );

            $vendorWiseCartList[$groupId] = [
                'seller_id' => $firstCartItem['seller_id'],
                'seller_is' => $firstCartItem['seller_is'],
                'grand_total' => $cartGroupGrandTotal,
                'order_amount' => $cartGroupOrderAmount,
                'coupon_code' => $couponInfo['coupon_code'] ?? 0,
                'coupon_bearer' => $couponInfo['coupon_bearer'] ?? 'inhouse',
                'coupon_discount' => $couponInfo['discount'] ?? 0,
                'discount_type' => $couponInfo['discount'] == 0 ? null : 'coupon_discount',
                'shipping_cost' => $shippingCost,
                'shipping_address_id' => OrderManager::getOrderAddressId(type: 'shipping_address', id: $data['address_id'] ?? null),
                'billing_address_id' => OrderManager::getOrderAddressId(type: 'billing_address', id: $data['billing_address_id'] ?? null),
                'shipping_type' => isset($shippingType?->shipping_type) ? $shippingType->shipping_type : 'order_wise',
                'is_shipping_free' => $isShippingFree,
                'shipping_method_id' => $shippingMethodId,
                'free_delivery_discount' => $freeShippingDiscount ?? 0,
                'extra_discount_type' => $extraDiscountType,
                'free_delivery_bearer' => $firstCartItem['seller_is'] == 'seller' ? $freeDeliveryBearer : 'admin',
                'refer_and_earn_discount' => $referAndEarnDiscount,
                'cart_list' => $cartListQueryGroupItem,
                'cart_group_id' => $groupId,
            ];
        }

        return $vendorWiseCartList;
    }

    public static function getOrderAddData(int $orderId, string $orderGroupId, object|array $customerData = [], object|array $cartData = [], object|array $orderData = []): array
    {
        $adminCommission = (float)str_replace(",", "", Helpers::sales_commission_before_order($cartData['cart_group_id'], $cartData['coupon_discount']));
        return [
            'id' => $orderId,
            'verification_code' => rand(100000, 999999),
            'customer_id' => $customerData['customer_id'],
            'is_guest' => $customerData['is_guest'],
            'seller_id' => $cartData['seller_id'],
            'seller_is' => $cartData['seller_is'],
            'customer_type' => 'customer',
            'payment_status' => $orderData['payment_status'],
            'order_status' => $orderData['order_status'],
            'payment_method' => $orderData['payment_method'],
            'transaction_ref' => $orderData['transaction_ref'] ?? null,
            'payment_by' => $orderData['payment_by'] ?? NULL,
            'payment_note' => $orderData['payment_note'] ?? NULL,
            'order_group_id' => $orderGroupId,
            'discount_amount' => $cartData['coupon_discount'],
            'discount_type' => $cartData['discount_type'],
            'coupon_code' => $cartData['coupon_code'],
            'coupon_discount_bearer' => $cartData['coupon_bearer'],
            'order_amount' => $cartData['order_amount'] - $cartData['refer_and_earn_discount'],
            'bring_change_amount' => $orderData['payment_method'] == 'cash_on_delivery' ? $orderData['bring_change_amount'] ?? 0 : null,
            'bring_change_amount_currency' => $orderData['bring_change_amount_currency'] ?? null,
            'admin_commission' => $adminCommission,
            'shipping_address' => $cartData['shipping_address_id'],
            'shipping_address_data' => ShippingAddress::find($cartData['shipping_address_id']),
            'billing_address' => getWebConfig('billing_input_by_customer') ? $cartData['billing_address_id'] : null,
            'billing_address_data' => getWebConfig('billing_input_by_customer') ? ShippingAddress::find($cartData['billing_address_id']) : null,
            'shipping_responsibility' => getWebConfig(name: 'shipping_method'),
            'shipping_cost' => $cartData['shipping_cost'],
            'extra_discount' => $cartData['free_delivery_discount'],
            'extra_discount_type' => $cartData['extra_discount_type'],
            'refer_and_earn_discount' => $cartData['refer_and_earn_discount'],
            'free_delivery_bearer' => $cartData['free_delivery_bearer'],
            'is_shipping_free' => $cartData['is_shipping_free'],
            'shipping_method_id' => $cartData['shipping_method_id'],
            'shipping_type' => $cartData['shipping_type'],
            'created_at' => now(),
            'updated_at' => now(),
            'order_note' => $orderData['order_note'] ?? session('order_note'),
        ];
    }

    public static function addOrderDetailsData(int $orderId, object|array|null $vendorCartList = []): void
    {
        foreach ($vendorCartList as $cartSingleItem) {
            $product = Product::where(['id' => $cartSingleItem['product_id']])->with(['digitalVariation', 'clearanceSale' => function ($query) {
                return $query->active();
            }])->first()->toArray();
            unset($product['is_shop_temporary_close']);
            unset($product['color_images_full_url']);
            unset($product['meta_image_full_url']);
            unset($product['images_full_url']);
            unset($product['reviews']);
            unset($product['translations']);

            $digitalProductVariation = DigitalProductVariation::with(['storage'])->where(['product_id' => $cartSingleItem['product_id'], 'variant_key' => $cartSingleItem['variant']])->first();
            if ($product['digital_product_type'] == 'ready_product' && $digitalProductVariation) {
                $getStoragePath = Storage::where([
                    'data_id' => $digitalProductVariation['id'],
                    "data_type" => "App\Models\DigitalProductVariation",
                ])->first();

                $product['digital_file_ready'] = $digitalProductVariation['file'];
                $product['storage_path'] = $getStoragePath ? $getStoragePath['value'] : 'public';
            } elseif ($product['digital_product_type'] == 'ready_product' && !empty($product['digital_file_ready'])) {
                $product['storage_path'] = $product['digital_file_ready_storage_type'] ?? 'public';
            }

            $price = $cartSingleItem['tax_model'] == 'include' ? $cartSingleItem['price'] - $cartSingleItem['tax'] : $cartSingleItem['price'];
            $productDiscount = getProductPriceByType(product: $product, type: 'discounted_amount', result: 'value', price: $cartSingleItem['price']);
            $orderDetails = [
                'order_id' => $orderId,
                'product_id' => $cartSingleItem['product_id'],
                'seller_id' => $cartSingleItem['seller_id'],
                'product_details' => json_encode($product),
                'qty' => $cartSingleItem['quantity'],
                'price' => $price,
                'tax' => $cartSingleItem['tax'] * $cartSingleItem['quantity'],
                'tax_model' => $cartSingleItem['tax_model'],
                'discount' => $productDiscount * $cartSingleItem['quantity'],
                'discount_type' => 'discount_on_product',
                'variant' => $cartSingleItem['variant'],
                'variation' => $cartSingleItem['variations'],
                'delivery_status' => 'pending',
                'shipping_method_id' => null,
                'payment_status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now()
            ];

            if ($cartSingleItem['variant'] != null) {
                $type = $cartSingleItem['variant'];
                $variationData = [];
                foreach (json_decode($product['variation'], true) as $var) {
                    if ($type == $var['type']) {
                        $var['qty'] -= $cartSingleItem['quantity'];
                    }
                    $variationData[] = $var;
                }
                Product::where(['id' => $product['id']])->update([
                    'variation' => json_encode($variationData),
                ]);
            }

            Product::where(['id' => $product['id']])->update([
                'current_stock' => $product['current_stock'] - $cartSingleItem['quantity']
            ]);

            DB::table('order_details')->insert($orderDetails);
        }
    }

    public static function generateOrder(object|array|null $data = []): array
    {
        $orderPlacedIds = [];
        $orderPlacedNotificationEvents = [];
        $orderPlacedMailEvents = [];
        $getCustomerInfo = OrderManager::getCustomerInfoForOrder(data: [
            'is_guest' => $data['is_guest'] ?? null,
            'guest_id' => $data['guest_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'newCustomerRegister' => $data['newCustomerRegister'] ?? null,
            'new_customer_id' => $data['new_customer_id'] ?? null,
            'requestObj' => $data['requestObj'] ?? null,
        ]);
        $orderGroupId = OrderManager::generateUniqueOrderID();
        $vendorWiseCartList = OrderManager::processOrderGenerateData(data: [
            'coupon_code' => $data['coupon_code'] ?? (session('coupon_code') ?? ''),
            'address_id' => $data['address_id'] ?? session('address_id'),
            'billing_address_id' => $data['billing_address_id'] ?? session('billing_address_id'),
            'requestObj' => $data['requestObj'] ?? null,
        ]);

        foreach ($vendorWiseCartList as $vendorWiseGroupId => $vendorWiseCart) {
            $order_id = OrderManager::generateNewOrderID();
            $orderPlacedIds[] = $order_id;

            $ordersData = OrderManager::getOrderAddData(
                orderId: $order_id,
                orderGroupId: $orderGroupId,
                customerData: $getCustomerInfo,
                cartData: $vendorWiseCart,
                orderData: $data,
            );
            DB::table('orders')->insertGetId($ordersData);

            if ($data['payment_method'] == 'offline_payment') {
                OfflinePayments::insert(['order_id' => $order_id, 'payment_info' => json_encode($data['offline_payment_info']), 'created_at' => Carbon::now()]);
            }

            self::add_order_status_history($order_id, $getCustomerInfo['customer_id'], $data['payment_status'] == 'paid' ? 'confirmed' : 'pending', 'customer');

            OrderManager::addOrderDetailsData(
                orderId: $order_id,
                vendorCartList: $vendorWiseCart['cart_list'],
            );

            $order = Order::with('customer', 'seller.shop', 'details')->find($order_id);
            OrderManager::getAddOrderTransactionsOnGenerateOrder(order: $order, ordersData: $ordersData);

            $orderPlacedNotificationEvents = OrderManager::getGenerateOrderNotificationInfo(
                vendorType: $vendorWiseCart['seller_is'],
                vendorId: $vendorWiseCart['seller_id'],
                order: $order,
                customer: $getCustomerInfo['customer'],
            );

            $orderPlacedMailEvents = OrderManager::getGenerateOrderMailInfo(
                vendorType: $vendorWiseCart['seller_is'],
                vendorId: $vendorWiseCart['seller_id'],
                vendorWiseCart: $vendorWiseCart,
                order: $order,
                customer: $getCustomerInfo['customer'],
            );
        }

        $user = Helpers::getCustomerInformation(($data['requestObj'] ?? request()->all()));

        $notificationSent = false;
        foreach ($orderPlacedIds as $orderPlacedId) {
            if (!$notificationSent && $user != 'offline') {
                $getOrder = Order::where('id', $orderPlacedId)->first();
                $referralUser = ReferralCustomer::where('user_id', $user['id'])->first();
                if ($referralUser && $referralUser->is_used != 1 && $referralUser->ordered_notify != 1) {
                    $orderPlacedNotificationEvents[] = [
                        'notification' => true,
                        'notificationData' => (object)[
                            'key' => 'your_referred_customer_has_been_place_order',
                            'type' => 'promoter',
                            'order' => $getOrder,
                        ],
                    ];
                }
                $notificationSent = true;
            }
        }

        if ($user != 'offline') {
            ReferralCustomer::where('user_id', $user['id'])->update(['is_used' => 1]);
        }

        foreach ($orderPlacedNotificationEvents as $orderPlacedEvent) {
            if (!empty($orderPlacedEvent)) {
                event(new OrderPlacedEvent(notification: $orderPlacedEvent['notificationData']));
            }
        }

        foreach ($orderPlacedMailEvents as $orderPlacedMailEvent) {
            try {
                event(new OrderPlacedEvent(email: $orderPlacedMailEvent['email'], data: $orderPlacedMailEvent['data']));
            } catch (Exception $exception) {
            }
        }

        CartManager::cartCleanByCartGroupIds(cartGroupIDs: collect($vendorWiseCartList)?->pluck('cart_group_id')->toArray() ?? []);

        session()->forget('coupon_code');
        session()->forget('coupon_type');
        session()->forget('coupon_bearer');
        session()->forget('coupon_discount');
        session()->forget('coupon_seller_id');

        return $orderPlacedIds;
    }

    public static function getAddOrderTransactionsOnGenerateOrder($order, $ordersData): void
    {
        if ($ordersData['payment_method'] != 'cash_on_delivery' && $ordersData['payment_method'] != 'offline_payment') {
            $orderSummary = OrderManager::getOrderTotalAndSubTotalAmountSummary($order);
            $orderAmount = $orderSummary['subtotal'] - $orderSummary['total_discount_on_product'] - $order['discount'];

            DB::table('order_transactions')->insert([
                'transaction_id' => OrderManager::generateUniqueOrderID(),
                'customer_id' => $order['customer_id'],
                'seller_id' => $order['seller_id'],
                'seller_is' => $order['seller_is'],
                'order_id' => $order['id'],
                'order_amount' => $orderAmount,
                'seller_amount' => $orderAmount - $ordersData['admin_commission'],
                'admin_commission' => $ordersData['admin_commission'],
                'received_by' => 'admin',
                'status' => 'hold',
                'delivery_charge' => $order['shipping_cost'] - $order['extra_discount'],
                'tax' => $orderSummary['total_tax'],
                'delivered_by' => 'admin',
                'payment_method' => $ordersData['payment_method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            OrderManager::getCheckOrCreateAdminWallet();
            AdminWallet::where(['admin_id' => $order['seller_id']])->increment('pending_amount', $order['order_amount']);
        }
    }

    public static function getGenerateOrderNotificationInfo(string $vendorType, int $vendorId, object|array $order, mixed $customer): array
    {
        $orderPlacedNotificationEvents = [];
        if ($vendorType == 'seller') {
            $seller = Seller::find($vendorId);
            if ($seller) {
                $orderPlacedNotificationEvents[] = [
                    'notification' => true,
                    'notificationData' => (object)[
                        'key' => 'new_order_message',
                        'type' => 'seller',
                        'order' => $order,
                    ],
                ];
            }
        }
        if ($customer != 'offline') {
            if ($order['payment_method'] != 'cash_on_delivery' && $order['payment_method'] != 'offline_payment') {
                $orderPlacedNotificationEvents[] = [
                    'notification' => true,
                    'notificationData' => (object)[
                        'key' => 'confirmed',
                        'type' => 'customer',
                        'order' => $order,
                    ],
                ];
            } else {
                $orderPlacedNotificationEvents[] = [
                    'notification' => true,
                    'notificationData' => (object)[
                        'key' => 'pending',
                        'type' => 'customer',
                        'order' => $order,
                    ],
                ];
            }
        }
        return $orderPlacedNotificationEvents;
    }

    public static function getGenerateOrderMailInfo(string $vendorType, int $vendorId, object|array $vendorWiseCart, object|array $order, mixed $customer): array
    {
        $orderPlacedMailEvents = [];
        $emailServicesSmtp = getWebConfig(name: 'mail_config');
        if ($emailServicesSmtp['status'] == 0) {
            $emailServicesSmtp = getWebConfig(name: 'mail_config_sendgrid');
        }
        if ($emailServicesSmtp['status'] == 1) {
            if ($customer == 'offline') {
                $offlineUser = ShippingAddress::where('id', $vendorWiseCart['shipping_address_id'])->first();
                if (!$offlineUser) {
                    $offlineUser = ShippingAddress::find($vendorWiseCart['billing_address_id']);
                }
                $email = $offlineUser['email'];
                $userName = $offlineUser['contact_person_name'];
            } else {
                $email = $customer['email'];
                $userName = $customer['f_name'];
            }

            $vendor = $vendorType == 'admin' ? Admin::find($vendorId) : Seller::find($vendorId);

            $orderPlacedMailEvents[] = [
                'email' => $email,
                'data' => [
                    'subject' => translate('order_placed'),
                    'title' => translate('order_placed'),
                    'userName' => $userName,
                    'userType' => 'customer',
                    'templateName' => 'order-place',
                    'order' => $order,
                    'orderId' => $order['id'],
                    'shopName' => $vendor?->shop?->name ?? getWebConfig('company_name'),
                    'shopId' => $vendor?->shop?->id ?? 0,
                    'attachmentPath' => self::storeInvoice($order['id']),
                ]
            ];

            $orderPlacedMailEvents[] = [
                'email' => $vendor['email'],
                'data' => [
                    'subject' => translate('new_order_received'),
                    'title' => translate('new_order_received'),
                    'userType' => $vendorType == 'admin' ? 'admin' : 'vendor',
                    'templateName' => 'order-received',
                    'order' => $order,
                    'orderId' => $order['id'],
                    'vendorName' => $vendor?->f_name,
                    'adminName' => $vendor?->name,
                ]
            ];
        }
        return $orderPlacedMailEvents;
    }

    public static function createWalletTransaction($user_id, float $amount, $transaction_type, $reference, $payment_data = []): bool|WalletTransaction
    {
        if (BusinessSetting::where('type', 'wallet_status')->first()->value != 1) return false;
        $user = User::find($user_id);
        $currentBalance = $user->wallet_balance;

        $walletTransaction = new WalletTransaction();
        $walletTransaction->user_id = $user->id;
        $walletTransaction->transaction_id = \Str::uuid();
        $walletTransaction->reference = $reference;
        $walletTransaction->transaction_type = $transaction_type;
        $walletTransaction->payment_method = isset($payment_data['payment_method']) ? $payment_data['payment_method'] : null;

        $debit = 0.0;
        $credit = 0.0;
        $addFundToWalletBonus = 0;

        if (in_array($transaction_type, ['add_fund_by_admin', 'add_fund', 'order_refund', 'loyalty_point'])) {
            $credit = $amount;
            if ($transaction_type == 'add_fund') {
                $walletTransaction->admin_bonus = Helpers::add_fund_to_wallet_bonus(Convert::usd($amount ?? 0));
                $addFundToWalletBonus = Helpers::add_fund_to_wallet_bonus(Convert::usd($amount ?? 0));
            } else if ($transaction_type == 'loyalty_point') {
                $credit = (($amount / BusinessSetting::where('type', 'loyalty_point_exchange_rate')->first()->value) * Convert::default(1));
            }
        } else if ($transaction_type == 'order_place') {
            $debit = $amount;
        }

        $creditAmount = currencyConverter($credit);
        $debitAmount = currencyConverter($debit);
        $walletTransaction->credit = $creditAmount;
        $walletTransaction->debit = $debitAmount;
        $walletTransaction->balance = $currentBalance + $creditAmount - $debitAmount;
        $walletTransaction->created_at = now();
        $walletTransaction->updated_at = now();
        $user->wallet_balance = $currentBalance + $addFundToWalletBonus + $creditAmount - $debitAmount;

        try {
            DB::beginTransaction();
            $user->save();
            $walletTransaction->save();
            DB::commit();
            if (in_array($transaction_type, ['loyalty_point', 'order_place', 'add_fund_by_admin'])) return $walletTransaction;
            return true;
        } catch (Exception $ex) {
            info($ex);
            DB::rollback();
            return false;
        }
    }

    public static function generateOrderAgain($request): array
    {
        $orderProducts = OrderDetail::where('order_id', $request->order_id)->get();
        $orderProductCount = $orderProducts->count();
        $addToCartCount = 0;
        $failedAddToCartCount = 0;

        foreach ($orderProducts as $key => $orderProduct) {
            $product = Product::active()->where(['id' => $orderProduct->product_id])->with(['digitalVariation'])->first();

            if ($product) {
                $productValid = true;
                if (($product['product_type'] == 'physical') && (($product['current_stock'] < $orderProduct['qty']) || ($product['minimum_order_qty'] > $product['current_stock']))) {
                    $productValid = false;
                }

                if ($productValid) {
                    $color = null;
                    $choices = [];
                    if ($orderProduct->variation) {
                        $variation = json_decode($orderProduct->variation, true);

                        if (isset($variation['color']) && $variation['color']) {
                            $color = Color::where('name', $variation['color'])->first()->code;
                            $i = 1;
                            foreach ($variation as $variationKey => $var) {
                                if ($variationKey != 'color') {
                                    $choices['choice_' . $i] = $var;
                                    $i++;
                                }
                            }
                        } else {
                            $i = 1;
                            foreach ($variation as $index => $var) {
                                if ($var) {
                                    $choices['choice_' . $i] = $var;
                                }
                                $i++;
                            }
                        }
                    }

                    $user = Helpers::getCustomerInformation($request);
                    // Generate Group ID Start
                    $cartCheck = Cart::where([
                        'customer_id' => $user->id,
                        'seller_id' => ($product->added_by == 'admin') ? 1 : $product->user_id,
                        'seller_is' => $product->added_by
                    ])->first();

                    if (isset($cartCheck)) {
                        $cartGroupId = $cartCheck['cart_group_id'];
                    } else {
                        $cartGroupId = $user->id . '-' . Str::random(5) . '-' . time();
                    }
                    // Generate Group ID End

                    $price = 0;
                    if (json_decode($product->variation)) {
                        $count = count(json_decode($product->variation));
                        for ($i = 0; $i < $count; $i++) {
                            if (json_decode($product->variation)[$i]->type == $orderProduct->variant) {
                                $price = json_decode($product->variation)[$i]->price;
                                if (json_decode($product->variation)[$i]->qty < $orderProduct->qty) {
                                    $productValid = false;
                                }
                            }
                        }
                    } else {
                        $price = $product->unit_price;
                    }

                    if ($product->product_type == 'digital') {
                        if ($product->digital_product_type == "ready_after_sell") {
                            $price = $product->unit_price;
                        } elseif ($product->digital_product_type == "ready_product" && !empty($product->digital_file_ready)) {
                            $price = $product->unit_price;
                        } elseif ($product->digital_product_type == "ready_product" && empty($product->digital_file_ready) && $product->digitalVariation) {
                            $productValid = false;
                            foreach ($product->digitalVariation as $digitalVariation) {
                                if ($digitalVariation['variant_key'] == $orderProduct['variant']) {
                                    $price = $digitalVariation['price'];
                                    $productValid = true;
                                }
                            }
                        }
                    }

                    $tax = Helpers::tax_calculation(product: $product, price: $price, tax: $product['tax'], tax_type: 'percent');
                    if ($productValid && $price != 0) {
                        $cartExist = Cart::where(['customer_id' => $user->id, 'variations' => $orderProduct->variation, 'product_id' => $orderProduct->product_id])->first();
                        $orderProductQuantity = $orderProduct->qty < $product['minimum_order_qty'] ? $product['minimum_order_qty'] : $orderProduct->qty;

                        if (!$cartExist) {
                            $cart = new Cart();
                            $cart['cart_group_id'] = $cartGroupId;
                            $cart['color'] = $color;
                            $cart['product_id'] = $orderProduct->product_id;
                            $cart['product_type'] = $product->product_type;
                            $cart['choices'] = json_encode($choices);
                            $cart['variations'] = !is_null($color) || !empty($choices) ? $orderProduct->variation : json_encode([]);
                            $cart['variant'] = $orderProduct->variant;
                            $cart['customer_id'] = $user->id ?? 0;
                            $cart['quantity'] = $orderProductQuantity;
                            $cart['price'] = $price;
                            $cart['tax'] = $tax;
                            $cart['tax_model'] = $product->tax_model;
                            $cart['slug'] = $product->slug;
                            $cart['name'] = $product->name;
                            $cart['is_checked'] = 1;
                            $cart['discount'] = Helpers::getProductDiscount($product, $price);
                            $cart['thumbnail'] = $product->thumbnail;
                            $cart['seller_id'] = ($product->added_by == 'admin') ? 1 : $product->user_id;
                            $cart['seller_is'] = $product->added_by;
                            $cart['shipping_cost'] = $product->product_type == 'physical' ? CartManager::get_shipping_cost_for_product_category_wise($product, $orderProductQuantity) : 0;
                            if ($product->added_by == 'seller') {
                                $cart['shop_info'] = Shop::where(['seller_id' => $product->user_id])->first()->name;
                            } else {
                                $cart['shop_info'] = getWebConfig(name: 'company_name');
                            }

                            $shippingMethod = getWebConfig(name: 'shipping_method');

                            if ($shippingMethod == 'inhouse_shipping') {
                                $adminShipping = ShippingType::where('seller_id', 0)->first();
                                $shippingType = isset($adminShipping) ? $adminShipping->shipping_type : 'order_wise';
                            } else {
                                if ($product->added_by == 'admin') {
                                    $adminShipping = ShippingType::where('seller_id', 0)->first();
                                    $shippingType = isset($adminShipping) ? $adminShipping->shipping_type : 'order_wise';
                                } else {
                                    $seller_shipping = ShippingType::where('seller_id', $product->user_id)->first();
                                    $shippingType = isset($seller_shipping) ? $seller_shipping->shipping_type : 'order_wise';
                                }
                            }

                            $cart['shipping_type'] = $shippingType;
                            $cart->save();
                        } else {
                            $cart['is_checked'] = 1;
                            $cartExist->quantity = $orderProductQuantity;
                            $cartExist->save();
                        }
                        $addToCartCount++;
                    } else {
                        $failedAddToCartCount++;
                    }
                } else {
                    $failedAddToCartCount++;
                }
            }
        }

        return [
            'order_product_count' => $orderProductCount,
            'add_to_cart_count' => $addToCartCount,
            'failedAddToCartCount' => $failedAddToCartCount,
        ];
    }

    public static function verifyCartListMinimumOrderAmount($request, $cart_group_id = null): array
    {
        $user = Helpers::getCustomerInformation($request);
        $status = 1;
        $amount = 0;
        $shippingCost = 0;
        $messages = [];
        $minimumOrderAmount = 0;
        $minimumOrderAmountStatus = getWebConfig(name: 'minimum_order_amount_status');
        $minimumOrderAmountBySeller = getWebConfig(name: 'minimum_order_amount_by_seller');
        $inhouseMinimumOrderAmount = getWebConfig(name: 'minimum_order_amount');
        $decimal_point_settings = getWebConfig(name: 'decimal_point_settings');

        if ($minimumOrderAmountStatus) {
            $query = Cart::whereHas('product', function ($query) {
                return $query->active();
            })->with(['seller', 'allProducts' => function ($query) {
                return $query->active();
            }])
                ->where([
                    'customer_id' => ($user == 'offline' ? (session('guest_id') ?? $request->guest_id) : $user->id),
                    'is_guest' => ($user == 'offline' ? 1 : '0'),
                    'is_checked' => 1,
                ]);

            if ($cart_group_id) {
                $cartItem = $query->where('cart_group_id', $cart_group_id)->first();

                if (isset($cartItem) && $cartItem->allProducts) {
                    if ($cartItem->allProducts->added_by == 'admin') {
                        $minimumOrderAmount = $inhouseMinimumOrderAmount;
                    } else {
                        $minimumOrderAmount = $minimumOrderAmountBySeller ? ($cartItem?->seller?->minimum_order_amount ?? 0) : 0;
                    }

                    $shippingCost = CartManager::get_shipping_cost(groupId: $cart_group_id, type: 'checked');
                    $newAmount = CartManager::cart_grand_total(cartGroupId: $cart_group_id, type: 'checked') - $shippingCost;
                    $status = $minimumOrderAmount > $newAmount ? 0 : 1;

                    if ($minimumOrderAmount > $newAmount) {
                        $status = 0;
                        $shopIdentity = $cartItem->allProducts->added_by == 'admin' ? getWebConfig(name: 'company_name') : $cartItem->seller->shop->name;
                        if (isset($request['payment_request_from']) && $request['payment_request_from'] == 'app') {
                            $messages[] = translate('Please_complete_minimum_Order_Amount') . ' ' . translate('for') . ' ' . $shopIdentity;
                        } else {
                            $messages[] = translate('minimum_Order_Amount') . ' ' . webCurrencyConverter(amount: $minimumOrderAmount) . ' ' . translate('for') . ' ' . $shopIdentity;
                        }
                    }
                    $amount = $amount + $newAmount;
                }
            } else {
                $cartGroups = $query->get()->groupBy('cart_group_id');
                foreach ($cartGroups as $group_key => $cart_group) {
                    $cartGroupFirstItem = $cart_group->first();
                    $seller = $cartGroupFirstItem->seller_is;
                    if ($seller == 'admin') {
                        $minimumOrderAmount = $inhouseMinimumOrderAmount;
                    } else {
                        $minimumOrderAmount = $minimumOrderAmountBySeller ? ($cartGroupFirstItem?->seller?->minimum_order_amount ?? 0) : 0;
                    }

                    $shippingCost = CartManager::get_shipping_cost(groupId: $group_key, type: 'checked');
                    $newAmount = CartManager::cart_grand_total(cartGroupId: $group_key, type: 'checked') - $shippingCost;

                    if ($minimumOrderAmount > $newAmount) {
                        $status = 0;
                        $shopIdentity = $seller == 'admin' ? getWebConfig(name: 'company_name') : $cartGroupFirstItem->seller->shop->name;
                        if (isset($request['payment_request_from']) && $request['payment_request_from'] == 'app') {
                            $messages[] = translate('Please_complete_minimum_Order_Amount') . ' ' . translate('for') . ' ' . $shopIdentity;
                        } else {
                            $messages[] = translate('minimum_Order_Amount') . ' ' . webCurrencyConverter(amount: $minimumOrderAmount) . ' ' . translate('for') . ' ' . $shopIdentity;
                        }
                    }
                    $amount = $amount + $newAmount;
                }
            }
        }

        return [
            'minimum_order_amount' => $minimumOrderAmount ?? 0,
            'amount' => $amount ? floatval($amount) : 0,
            'status' => $status,
            'messages' => $messages,
            'shippingCost' => $shippingCost ?? 0,
            'cart_group_id' => $cart_group_id ?? null
        ];
    }

    public static function checkSingleProductMinimumOrderAmountVerify($request, $product, $totalAmount): array
    {
        $status = 1;
        $message = '';
        $minimumOrderAmount = 0;
        $minimumOrderAmountStatus = getWebConfig(name: 'minimum_order_amount_status');
        $minimumOrderAmountBySeller = getWebConfig(name: 'minimum_order_amount_by_seller');
        $inhouseMinimumOrderAmount = getWebConfig(name: 'minimum_order_amount');

        if ($minimumOrderAmountStatus) {
            if ($product->added_by == 'admin') {
                $minimumOrderAmount = $inhouseMinimumOrderAmount;
            } else {
                $minimumOrderAmount = $minimumOrderAmountBySeller ? ($product->seller->minimum_order_amount ?? 0) : 0;
            }
            $status = $minimumOrderAmount > $totalAmount ? 0 : 1;

            if ($minimumOrderAmount > $totalAmount) {
                $shopIdentity = $product->added_by == 'admin' ? getWebConfig(name: 'company_name') : $product->seller->shop->name;
                $message = translate('minimum_Order_Amount') . ' ' . webCurrencyConverter(amount: $minimumOrderAmount) . ' ' . translate('for') . ' ' . $shopIdentity;
            }
        }

        return [
            'minimum_order_amount' => $minimumOrderAmount,
            'amount' => $totalAmount,
            'added_by' => $product->added_by,
            'status' => $status,
            'message' => $message,
        ];
    }

    public static function getFreeDeliveryOrderAmountArray($cart_group_id = null): array
    {
        $freeDeliveryData = [
            'amount' => 0, // free delivery amount
            'percentage' => 0, // completed percentage
            'amount_need' => 0, // need amount for free delivery
            'shipping_cost_saved' => 0,
            'cart_id' => $cart_group_id
        ];

        $freeDeliveryData['status'] = (int)(getWebConfig(name: 'free_delivery_status') ?? 0);
        $freeDeliveryData['responsibility'] = (string)getWebConfig(name: 'free_delivery_responsibility');
        $freeDeliveryOverAmount = (float)getWebConfig(name: 'free_delivery_over_amount');
        $freeDeliveryOverAmountSeller = (float)getWebConfig(name: 'free_delivery_over_amount_seller');

        if ($freeDeliveryData['status'] && $cart_group_id) {
            $getCartList = Cart::whereHas('product', function ($query) {
                return $query->active();
            })->where(['product_type' => 'physical', 'cart_group_id' => $cart_group_id, 'is_checked' => 1])->first();

            if ($getCartList) {
                if ($getCartList->seller_is == 'admin') {
                    $freeDeliveryData['amount'] = $freeDeliveryOverAmount;
                    $freeDeliveryData['status'] = $freeDeliveryOverAmount > 0 ? 1 : 0;
                } else {
                    $seller = Seller::where('id', $getCartList->seller_id)->first();
                    $freeDeliveryData['status'] = $seller->free_delivery_status ?? 0;

                    if ($freeDeliveryData['responsibility'] == 'admin') {
                        $freeDeliveryData['amount'] = $freeDeliveryOverAmountSeller;
                        $freeDeliveryData['status'] = $freeDeliveryOverAmountSeller > 0 ? 1 : 0;
                    }

                    if ($freeDeliveryData['responsibility'] == 'seller' && $freeDeliveryData['status'] == 1) {
                        $freeDeliveryData['amount'] = $seller->free_delivery_over_amount;
                        $freeDeliveryData['status'] = $seller->free_delivery_over_amount > 0 ? 1 : 0;
                    }
                }

                $amount = CartManager::getCartGrandTotalWithoutShippingCharge(cartGroupId: $getCartList->cart_group_id, type: 'checked');
                $freeDeliveryData['amount_need'] = $freeDeliveryData['amount'] - $amount;
                $freeDeliveryData['percentage'] = ($freeDeliveryData['amount'] > 0) && $amount > 0 && ($freeDeliveryData['amount'] >= $amount) ? number_format(($amount / $freeDeliveryData['amount']) * 100) : 100;
                if ($freeDeliveryData['status'] == 1 && $freeDeliveryData['percentage'] == 100) {
                    $freeDeliveryData['shipping_cost_saved'] = CartManager::get_shipping_cost(groupId: $getCartList->cart_group_id, type: 'checked');
                }
            } else {
                $freeDeliveryData['status'] = 0;
            }
        }

        return $freeDeliveryData;
    }

    public static function getOrderTotalAndSubTotalAmountSummary($order): array
    {
        $subTotal = 0;
        $totalTax = 0;
        $totalDiscountOnProduct = 0;
        foreach ($order->details as $key => $detail) {
            $subTotal += $detail->price * $detail->qty;
            $totalTax += $detail->tax;
            $totalDiscountOnProduct += $detail->discount;
        }
        $totalShippingCost = $order['shipping_cost'];
        return [
            'subtotal' => $subTotal,
            'total_tax' => $totalTax,
            'total_discount_on_product' => $totalDiscountOnProduct,
            'total_shipping_cost' => $totalShippingCost,
        ];
    }

    public static function getRefundDetailsForSingleOrderDetails($orderDetailsId): array
    {
        $orderDetails = OrderDetail::where(['id' => $orderDetailsId])->first();
        $order = Order::where(['id' => $orderDetails['order_id']])->with('details')->first();

        $totalProductPrice = 0;
        foreach ($order->details as $key => $orderDetail) {
            $totalProductPrice += ($orderDetail->qty * $orderDetail->price) + $orderDetail->tax - $orderDetail->discount;
        }
        $subtotal = ($orderDetails->price * $orderDetails->qty) - $orderDetails->discount + $orderDetails->tax;
        $couponDiscount = ($order->discount_amount * $subtotal) / $totalProductPrice;
        $referAndEarnDiscount = OrderManager::getReferDiscountAmountForSingleOrderDetails(orderDetailsId: $orderDetailsId);

        return [
            'product_price' => $orderDetails->price,
            'product_discount' => $orderDetails->discount,
            'tax' => $orderDetails->tax,
            'sub_total' => $subtotal,
            'coupon_discount' => $couponDiscount,
            'referral_discount' => $referAndEarnDiscount,
            'total_refundable_amount' => $subtotal - $couponDiscount - $referAndEarnDiscount,
        ];
    }

    public static function getReferDiscountAmountForSingleOrderDetails($orderDetailsId): int|float
    {
        $orderDetails = OrderDetail::where(['id' => $orderDetailsId])->first();
        $order = Order::where(['id' => $orderDetails['order_id']])->with('details')->first();

        $totalProductPriceWithOutDiscount = 0;
        foreach ($order->details as $key => $details) {
            $totalProductPriceWithOutDiscount += ($details['price'] - $details->discount) * $details['qty'];
        }
        $rate = (($orderDetails['price'] - $orderDetails->discount) * $orderDetails['qty'] * 100) / $totalProductPriceWithOutDiscount;
        $orderReferralDiscount = ($order?->refer_and_earn_discount ?? 0);
        return $orderReferralDiscount > 0 ? ($rate * $orderReferralDiscount) / 100 : 0;
    }

    public static function getRefundAmountFromOrderDetails($orderId, $orderDetailsId): int|float
    {
        $order = Order::where(['id' => $orderId])->with('details')->first();
        $orderDetails = OrderDetail::where(['id' => $orderDetailsId])->first();
        $totalProductPrice = 0;
        foreach ($order->details as $key => $details) {
            $totalProductPrice += ($details->qty * $details->price) + $details->tax - $details->discount;
        }
        $subtotal = ($orderDetails->price * $orderDetails->qty) - $orderDetails->discount + $orderDetails->tax;
        $couponDiscount = ($orderDetails->discount_amount * $subtotal) / $totalProductPrice;
        return $subtotal - $couponDiscount - OrderManager::getReferDiscountAmountForSingleOrderDetails($orderDetailsId);
    }

    public static function storeInvoice($id): string
    {
        $order = Order::with('seller')->with('shipping')->where('id', $id)->first();
        $invoiceSettings = getWebConfig('invoice_settings');
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice'], compact('order', 'invoiceSettings'));
        return self::storePdf(view: $mpdf_view, filePrefix: 'order_invoice_', filePostfix: $order['id'], pdfType: 'invoice', requestFrom: 'web');
    }


    public static function checkValidationForCheckoutPages(Request $request): array
    {
        $response['status'] = 1;
        $response['physical_product_view'] = false;
        $message = [];

        $verifyStatus = OrderManager::verifyCartListMinimumOrderAmount($request);
        if ($verifyStatus['status'] == 0) {
            $response['status'] = 0;
            $response['errorType'] = 'minimum-order-amount';
            $response['redirect'] = route('shop-cart');
            foreach ($verifyStatus['messages'] as $verifyStatusMessages) {
                $message[] = $verifyStatusMessages;
            }
        }

        $cartItemGroupIDsAll = CartManager::get_cart_group_ids($request);
        $cartItemGroupIDs = CartManager::get_cart_group_ids(request: $request, type: 'checked');
        $shippingMethod = getWebConfig(name: 'shipping_method');

        if (count($cartItemGroupIDsAll) <= 0) {
            $response['status'] = 0;
            $response['errorType'] = 'empty-cart';
            $response['redirect'] = url('/');
            $message[] = translate('no_items_in_basket');
        } elseif (count($cartItemGroupIDs) <= 0) {
            $response['status'] = 0;
            $response['errorType'] = 'empty-shipping';
            $response['redirect'] = route('shop-cart');
            $message[] = translate('Please_add_or_checked_items_before_proceeding_to_checkout');
        }

        $unavailableVendorsStatus = 0;
        $inhouseShippingMsgCount = 0;

        $isPhysicalProductExist = false;
        $productStockStatus = true;
        foreach ($cartItemGroupIDs as $groupId) {
            $isPhysicalProductExist = false;
            $cartList = Cart::whereHas('product', function ($query) {
                return $query->active();
            })->with(['product' => function ($query) {
                return $query->active();
            }])->where(['cart_group_id' => $groupId, 'is_checked' => 1])->get();
            foreach ($cartList as $cart) {
                if ($cart->product_type == 'physical') {
                    $isPhysicalProductExist = true;
                    $response['physical_product_view'] = true;
                }
            }

            $cartList = Cart::whereHas('product', function ($query) {
                return $query->active();
            })->with(['product' => function ($query) {
                return $query->active();
            }])->groupBy('cart_group_id')->where(['cart_group_id' => $groupId, 'is_checked' => 1])->get();
            $productStockCheck = CartManager::product_stock_check($cartList);
            if (!$productStockCheck) {
                $productStockStatus = false;
            }

            foreach ($cartList as $cartKey => $cart) {
                if ($cartKey == 0) {
                    $vendorType = $cart->seller_is == "seller" ? 'vendor' : 'inhouse';
                    $vendorInfo = $cart->seller_is == "seller" ? Shop::where('seller_id', $cart?->seller_id)->first() : null;
                    $temporaryClose = checkVendorAbility(type: $vendorType, status: 'temporary_close', vendor: $vendorInfo);
                    $vacationStatus = checkVendorAbility(type: $vendorType, status: 'vacation_status', vendor: $vendorInfo);
                    if ($temporaryClose || $vacationStatus) {
                        $unavailableVendorsStatus = 1;
                    }
                }
            }

            if ($isPhysicalProductExist) {
                foreach ($cartList as $cart) {
                    if ($shippingMethod == 'inhouse_shipping') {
                        $adminShipping = ShippingType::where('seller_id', 0)->first();
                        $shippingType = isset($adminShipping) ? $adminShipping->shipping_type : 'order_wise';
                    } else {
                        if ($cart->seller_is == 'admin') {
                            $adminShipping = ShippingType::where('seller_id', 0)->first();
                            $shippingType = isset($adminShipping) ? $adminShipping->shipping_type : 'order_wise';
                        } else {
                            $sellerShipping = ShippingType::where('seller_id', $cart->seller_id)->first();
                            $shippingType = isset($sellerShipping) ? $sellerShipping->shipping_type : 'order_wise';
                        }
                    }

                    if ($isPhysicalProductExist && $shippingType == 'order_wise') {
                        $sellerShippingCount = 0;
                        if ($shippingMethod == 'inhouse_shipping') {
                            $sellerShippingCount = ShippingMethod::where(['status' => 1])->where(['creator_type' => 'admin'])->count();
                            if ($sellerShippingCount <= 0 && isset($cart->seller->shop)) {
                                $message[] = translate('shipping_Not_Available_for') . ' ' . getWebConfig(name: 'company_name');
                                $response['status'] = 0;
                                $response['redirect'] = route('shop-cart');
                            }
                        } else {
                            if ($cart->seller_is == 'admin') {
                                $sellerShippingCount = ShippingMethod::where(['status' => 1])->where(['creator_type' => 'admin'])->count();
                                if ($sellerShippingCount <= 0 && isset($cart->seller->shop)) {
                                    $message[] = translate('shipping_Not_Available_for') . ' ' . getWebConfig(name: 'company_name');
                                    $response['status'] = 0;
                                    $response['redirect'] = route('shop-cart');
                                }
                            } else if ($cart->seller_is == 'seller') {
                                $sellerShippingCount = ShippingMethod::where(['status' => 1])->where(['creator_id' => $cart->seller_id, 'creator_type' => 'seller'])->count();
                                if ($sellerShippingCount <= 0 && isset($cart->seller->shop)) {
                                    $message[] = translate('shipping_Not_Available_for') . ' ' . $cart->seller->shop->name;
                                    $response['status'] = 0;
                                    $response['redirect'] = route('shop-cart');
                                }
                            }
                        }

                        if ($sellerShippingCount > 0 && $shippingMethod == 'inhouse_shipping' && $inhouseShippingMsgCount < 1) {
                            $cartShipping = CartShipping::where('cart_group_id', $cart->cart_group_id)->first();
                            if (!isset($cartShipping)) {
                                $response['status'] = 0;
                                $response['errorType'] = 'empty-shipping';
                                $response['redirect'] = route('shop-cart');
                                $message[] = translate('select_shipping_method');
                            }
                            $inhouseShippingMsgCount++;
                        } elseif ($sellerShippingCount > 0 && $shippingMethod != 'inhouse_shipping') {
                            $cartShipping = CartShipping::where('cart_group_id', $cart->cart_group_id)->first();
                            if (!isset($cartShipping)) {
                                $response['status'] = 0;
                                $response['errorType'] = 'empty-shipping';
                                $response['redirect'] = route('shop-cart');
                                $shopIdentity = $cart->seller_is == 'admin' ? getWebConfig(name: 'company_name') : $cart->seller->shop->name;
                                $message[] = translate('select') . ' ' . $shopIdentity . ' ' . translate('shipping_method');
                            }
                        }
                    }
                }
            }
        }

        if ($unavailableVendorsStatus) {
            $message[] = translate('please_remove_all_products_from_unavailable_vendors');
            $response['status'] = 0;
            $response['redirect'] = route('shop-cart');
        }

        if (!$productStockStatus) {
            $message[] = translate('Please_remove_this_unavailable_product_for_continue');
            $response['status'] = 0;
            $response['redirect'] = route('shop-cart');
        }

        $response['message'] = $message;
        return $response ?? [];
    }

    public static function getOrderTotalPriceSummary($order): array
    {
        $itemPrice = 0;
        $subTotal = 0;
        $total = 0;
        $taxTotal = 0;
        $itemDiscount = 0;
        $totalProductPrice = 0;
        $couponDiscount = 0;
        $referAndEarnDiscount = 0;
        $deliveryFeeDiscount = 0;
        $totalItemQuantity = 0;

        foreach ($order->details as $detailKey => $detail) {
            $itemPrice += $detail['price'] * $detail['qty'];
            $subTotal += $detail['price'] * $detail['qty'];
            $productPrice = ($detail['price'] * $detail['qty']);
            $totalProductPrice += $productPrice;
            $itemDiscount += $detail['discount'];
            $taxTotal += $detail['tax'];
            $totalItemQuantity += $detail['qty'];
        }
        $total = $itemPrice + $taxTotal - $itemDiscount;
        $shipping = $order['shipping_cost'];

        if ($order['extra_discount_type'] == 'percent') {
            $extraDiscount = (($totalProductPrice) / 100) * $order['extra_discount'];
        } else {
            $extraDiscount = $order['extra_discount'];
        }
        if (isset($order['discount_amount'])) {
            $couponDiscount = $order['discount_amount'];
        }
        if (isset($order['refer_and_earn_discount'])) {
            $referAndEarnDiscount = $order['refer_and_earn_discount'];
        }
        if ($order['is_shipping_free'] == 1) {
            $deliveryFeeDiscount = $shipping;
        }

        return [
            'itemPrice' => $itemPrice,
            'itemDiscount' => $itemDiscount,
            'extraDiscount' => $extraDiscount,
            'subTotal' => $subTotal - $itemDiscount - ($order['is_shipping_free'] == 1 ? 0 : $extraDiscount),
            'couponDiscount' => $couponDiscount,
            'referAndEarnDiscount' => $referAndEarnDiscount,
            'taxTotal' => $taxTotal,
            'shippingTotal' => $shipping,
            'deliveryFeeDiscount' => $deliveryFeeDiscount,
            'totalItemQuantity' => $totalItemQuantity,
            'totalAmount' => ($total + $shipping - $extraDiscount - $couponDiscount - $referAndEarnDiscount),
            'paidAmount' => $order['paid_amount'],
            'changeAmount' => ($order['paid_amount'] - ($total + $shipping - $extraDiscount - $couponDiscount - $referAndEarnDiscount)),
        ];
    }
}
