<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $imageFullUrl = $this->image_full_url;
        $imageUrl = null;
        if (!empty($imageFullUrl)) {
            if (is_array($imageFullUrl) && isset($imageFullUrl['path'])) {
                $imageUrl = $imageFullUrl['path'];
            } elseif (is_string($imageFullUrl)) {
                $imageUrl = $imageFullUrl;
            }
        }
        
        if (!$imageUrl) {
            $image = (string) $this->image;
            $isDefault = ($image === '' || $image === 'def.png' || $image === 'null');
            $imageUrl = $isDefault ? null : asset('storage/app/public/shop/' . $image);
        }

        return [
            'id'             => (int) $this->id,
            'name'           => (string) $this->name,
            'image'          => $imageUrl,
            'products_count' => (int) ($this->products_count ?? 0),
        ];
    }
}
