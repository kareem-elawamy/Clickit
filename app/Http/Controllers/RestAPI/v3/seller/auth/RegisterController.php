<?php

namespace App\Http\Controllers\RestAPI\v3\seller\auth;

use App\Events\VendorRegistrationEvent;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Shop;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:sellers',
            'shop_address' => 'required',
            'f_name' => 'required',
            'l_name' => 'required',
            'shop_name' => 'required',
            'phone' => 'required|unique:sellers',
            'password' => 'required|min:8',
            'image' => 'required|mimes: jpg,jpeg,png,gif',
            'logo' => 'required|mimes: jpg,jpeg,png,gif',
            'banner' => 'required|mimes: jpg,jpeg,png,gif',
            'bottom_banner' => 'mimes: jpg,jpeg,png,gif',
            'tax_identification_number' => 'nullable|string',
            'tin_expire_date' => 'nullable|date|after_or_equal:today',
            'tin_certificate' => 'nullable|mimes:pdf,doc,docx,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => Helpers::validationErrorProcessor($validator)], 403);
        }

        $storage = config('filesystems.disks.default') ?? 'public';
        DB::beginTransaction();
        try {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller->phone = $request->phone;
            $seller->email = $request->email;
            $seller->image = $request->file('image') ? ImageManager::upload('seller/', 'webp', $request->file('image')) : null;
            $seller->password = bcrypt($request->password);
            $seller->status = $request->status == 'approved' ? 'approved' : "pending";
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;
            $shop->contact = $request->phone;
            $shop->image = $request->file('logo') ? ImageManager::upload('shop/', 'webp', $request->file('logo')) : null;
            $shop->image_storage_type = $request->has('logo') ? $storage : null;
            $shop->banner =  $request->file('banner') ? ImageManager::upload('shop/banner/', 'webp', $request->file('banner')) : null;
            $shop->banner_storage_type = $request->has('banner') ? $storage : null;
            $shop->bottom_banner = ImageManager::upload('shop/banner/', 'webp', $request->file('bottom_banner'));
            $shop->bottom_banner_storage_type = $request->has('bottom_banner') ? $storage : null;
            $shop->tax_identification_number = $request['tax_identification_number'] ?? '';
            $shop->tin_expire_date = $request['tin_expire_date'] ? Carbon::parse($request['tin_expire_date']) : null;
            $shop->tin_certificate = $request->file('tin_certificate') ? ImageManager::file_upload(
                dir: 'shop/documents/',
                format: $request->file('tin_certificate')->getClientOriginalExtension(),
                file: $request->file('tin_certificate')) : null;
            $shop->tin_certificate_storage_type = $request->has('tin_certificate') ? $storage : null;
            $shop->save();

            DB::table('seller_wallets')->insert([
                'seller_id' => $seller['id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            $data = [
                'vendorName' => $request['f_name'],
                'status' => 'pending',
                'subject' => translate('Vendor_Registration_Successfully_Completed'),
                'title' => translate('Vendor_Registration_Successfully_Completed'),
                'userType' => 'vendor',
                'templateName' => 'registration',
            ];
            event(new VendorRegistrationEvent(email: $request['email'], data: $data));
            return response()->json(['message' => 'Shop apply successfully!'], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Shop apply fail!',
                'error' => $e->getMessage(),
            ], 403);
        }

    }
}
