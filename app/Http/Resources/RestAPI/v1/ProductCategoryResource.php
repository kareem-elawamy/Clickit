<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thumbnail = (string) $this->thumbnail;
        $isDefault = ($thumbnail === '' || $thumbnail === 'def.png' || $thumbnail === 'null');
        
        $thumbnailUrl = null;
        if (!empty($this->thumbnail_full_url)) {
            if (is_array($this->thumbnail_full_url) && isset($this->thumbnail_full_url['path'])) {
                $thumbnailUrl = $this->thumbnail_full_url['path'];
            } elseif (is_string($this->thumbnail_full_url)) {
                $thumbnailUrl = $this->thumbnail_full_url;
            }
        }

        if (!$thumbnailUrl) {
            $thumbnailUrl = $isDefault ? null : asset('storage/app/public/product/thumbnail/' . $thumbnail);
        }

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'thumbnail'      => $thumbnailUrl,
            'unit_price'     => (float) $this->unit_price,
            'discount'       => (float) $this->discount,
            'rating'         => (float) ($this->reviews_avg_rating ?? 0.0),
            'reviews_count'  => (int) ($this->reviews_count ?? 0),
            'shop_name'      => $this->seller?->shop?->name ?? 'Admin',
        ];
    }
}
