<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerThinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shop = $this->whenLoaded('shop');
        
        $logoUrl = null;
        if ($shop) {
            $image = (string) $shop->image;
            $isDefault = ($image === '' || $image === 'def.png' || $image === 'null');

            if (!empty($shop->image_full_url)) {
                if (is_array($shop->image_full_url) && isset($shop->image_full_url['path'])) {
                    $logoUrl = $shop->image_full_url['path'];
                } elseif (is_string($shop->image_full_url)) {
                    $logoUrl = $shop->image_full_url;
                }
            }

            if (!$logoUrl) {
                $logoUrl = $isDefault ? null : asset('storage/app/public/shop/' . $image);
            }
        }

        return [
            'id'     => $this->id,
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'shop'   => $shop ? [
                'id'   => $shop->id,
                'name' => $shop->name,
                'logo' => $logoUrl
            ] : null,
        ];
    }
}
