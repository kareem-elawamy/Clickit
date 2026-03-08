<link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/pos-invoice.css') }}">
<?php
$orderTotalPriceSummary = \App\Utils\OrderManager::getOrderTotalPriceSummary(order: $order);
?>
<div class="width-363px">
    <div class="text-center pt-4 mb-3">
        <h2 class="line-height-1">{{ getWebConfig('company_name') }}</h2>
        <h5 class="line-height-1 font-size-16px">
            {{ translate('phone') }} : {{ getWebConfig('company_phone') }}
        </h5>
    </div>

    <span class="dashed-hr"></span>
    <div class="row mt-3">
        <div class="col-6">
            <h5>{{ translate('order_ID') }} : {{ $order['id'] }}</h5>
        </div>
        <div class="col-6">
            <h5 class="">
                {{ date('d/M/Y h:i a', strtotime($order['created_at'])) }}
            </h5>
        </div>
        @if($order->customer)
            <div class="col-12">
                <h5 class="text-capitalize">{{ translate('customer_name') }} : {{$order->customer['f_name'].' '.$order->customer['l_name']}}</h5>
                @if ($order->customer->id !=0)
                    <h5>{{ translate('phone') }} : {{$order->customer['phone']}}</h5>
                @endif

            </div>
        @endif
    </div>
    <h5 class="text-uppercase"></h5>
    <span class="dashed-hr"></span>
    <table class="table table-bordered mt-3 text-left width-99">
        <thead>
        <tr>
            <th class="text-center text-uppercase">{{ translate('qty') }}</th>
            <th class="text-left text-uppercase">{{ translate('desc') }}</th>
            <th class="text-center">{{ translate('price') }}</th>
        </tr>
        </thead>

        <tbody>
        @php($sub_total=0)
        @php($total_tax=0)
        @php($total_dis_on_pro=0)
        @php($product_price=0)
        @php($total_product_price=0)
        @php($ext_discount=0)
        @php($coupon_discount=0)
        @foreach($order->details as $detail)
            @if($detail->product)
                <tr>
                    <td class="text-left">
                        {{$detail['qty']}}
                    </td>
                    <td class="text-left">
                        <span> {{ Str::limit($detail->product['name'], 200) }}</span><br>
                        @php($detailVariationArray=json_decode($detail['variation'], true))
                        @php($detailVariationCount=0)
                        @foreach($detailVariationArray as $key1 => $variation)
                            @if(!empty($key1) && !empty($variation))
                                @php($detailVariationCount++)
                            @endif
                        @endforeach
                        @if($detail->product->product_type == 'physical' && $detailVariationCount > 0)
                            <strong><u>{{ translate('variation') }} : </u></strong>
                            @foreach($detailVariationArray as $key1 => $variation)
                                @if(!empty($key1) && !empty($variation))
                                    <div class="font-size-sm text-body color-black">
                                        <span>{{ translate($key1) }}:</span>
                                        <span class="font-weight-bold">{{ $variation }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        @if(isset($detail['discount']) && $detail['discount'] > 0)
                            {{ translate('discount') }} :
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail['discount']), currencyCode: getCurrencyCode()) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @php($amount=($detail['price']*$detail['qty'])-$detail['discount'])
                        @php($product_price = $detail['price']*$detail['qty'])
                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $amount), currencyCode: getCurrencyCode()) }}
                    </td>
                </tr>
                @php($sub_total+=$amount)
                @php($total_product_price+=$product_price)
                @php($total_tax+=$detail['tax'])

            @endif
        @endforeach
        </tbody>
    </table>
    <span class="dashed-hr"></span>

    <table class="w-100 color-black">
        <tr>
            <td colspan="2"></td>
            <td class="text-end">{{ translate('items_Price') }}:</td>
            <td class="text-end">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['itemPrice']), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-end">{{ translate('item_discount') }}:</td>
            <td class="text-end">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['itemDiscount']), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-end">{{ translate('extra_discount') }}:</td>
            <td class="text-end">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['extraDiscount']), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-end">{{ translate('subtotal') }}:</td>
            <td class="text-end">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['subTotal']), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-end">{{ translate('tax') }} / {{ translate('VAT') }}:</td>
            <td class="text-end">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['taxTotal']), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-end">{{ translate('coupon_discount') }}:</td>
            <td class="text-end">-{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['couponDiscount']), currencyCode: getCurrencyCode()) }}</td>
        </tr>
        @if($orderTotalPriceSummary['referAndEarnDiscount'] > 0)
            <tr>
                <td colspan="2"></td>
                <td class="text-end">{{ translate('referral_discount') }}:</td>
                <td class="text-end">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['referAndEarnDiscount']), currencyCode: getCurrencyCode()) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="2"></td>
            <td class="text-end font-size-20px">
                {{ translate('total') }}:
            </td>
            <td class="text-end font-size-20px">
                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['totalAmount']), currencyCode: getCurrencyCode()) }}
            </td>
        </tr>
        @if ($order->order_type == 'pos' || $order->order_type == 'POS')
            <tr>
                <td colspan="4">
                    <span class="dashed-hr"></span>
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td class="text-end">
                    {{ translate('Paid_Amount') }}:
                </td>
                <td class="text-end">
                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['paidAmount']), currencyCode: getCurrencyCode()) }}
                </td>
            </tr>

            <tr>
                <td colspan="2"></td>
                <td class="text-end">
                    {{ translate('Change_Amount') }}:
                </td>
                <td class="text-end">
                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderTotalPriceSummary['changeAmount']), currencyCode: getCurrencyCode()) }}
                </td>
            </tr>
        @endif
    </table>


    <div class="d-flex flex-row justify-content-between border-top">
        <span class="mt-2">{{ translate('paid_by') }}: {{ translate($order->payment_method) }}</span>
    </div>
    <span class="dashed-hr"></span>
    <h5 class="text-center pt-3 text-uppercase">
        """{{ translate('thank_you') }}"""
    </h5>
    <span class="dashed-hr"></span>
</div>
