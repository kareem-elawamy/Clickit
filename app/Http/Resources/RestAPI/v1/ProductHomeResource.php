<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $thumbnail = (string) $this->thumbnail;
        $isDefault = ($thumbnail === '' || $thumbnail === 'def.png' || $thumbnail === 'null');
        
        $thumbnailUrl = $isDefault 
            ? null 
            : asset('storage/app/public/product/thumbnail/' . $thumbnail);

        return [
            'id'            => (int) $this->id,
            'name'          => (string) $this->name,
            'thumbnail'     => $thumbnailUrl,
            'unit_price'    => (float) $this->unit_price,
            'discount'      => (float) $this->discount,
            'discount_type' => (string) $this->discount_type,
            'rating'        => (float) ($this->reviews_avg_rating ?? 0.0),
            'review_count'  => (int) ($this->reviews_count ?? 0),
            'shop_name'     => (string) ($this->seller?->shop?->name ?? 'Admin'),
        ];
    }
}
