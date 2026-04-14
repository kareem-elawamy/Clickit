<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // ─── Thumbnail ─────────────────────────────────────────────────────────
        $thumbnail = (string) $this->thumbnail;
        $isDefault = ($thumbnail === '' || $thumbnail === 'def.png' || $thumbnail === 'null');
        $thumbnailUrl = $isDefault
            ? null
            : asset('storage/app/public/product/thumbnail/' . $thumbnail);

        // ─── Gallery Images ──────────────────────────────────────────────────────
        $images = [];
        $rawImages = $this->images;
        if (!is_array($rawImages)) {
            $rawImages = json_decode(is_string($rawImages) && $rawImages !== '' ? $rawImages : '[]', true) ?: [];
        }
        foreach ($rawImages as $img) {
            if (is_array($img)) {
                $fname = $img['image_name'] ?? '';
            } elseif (is_object($img)) {
                $fname = $img->image_name ?? '';
            } else {
                $fname = (string) $img;
            }

            if (!empty($fname) && $fname !== 'def.png' && $fname !== 'null') {
                $images[] = asset('storage/app/public/product/' . $fname);
            }
        }
        if (empty($images)) {
            $images[] = $thumbnailUrl;
        }

        // ─── Seller & Shop ────────────────────────────────────────────────────
        $seller = $this->whenLoaded('seller');
        $sellerData = null;
        if ($seller) {
            $shop = $seller->shop;
            $sellerData = [
                'id'        => $seller->id,
                'shop_id'   => $shop?->id,
                'shop_name' => $shop?->name ?? 'Admin',
            ];
        }

        // ─── Variations (cleaned) ─────────────────────────────────────────────
        $rawVariation = $this->variation;
        $variation = is_array($rawVariation) ? $rawVariation : (json_decode(is_string($rawVariation) && $rawVariation !== '' ? $rawVariation : '[]', true) ?: []);

        // ─── Choice Options (cleaned) ──────────────────────────────────────────
        $rawChoiceOptions = $this->choice_options;
        $choiceOptions = is_array($rawChoiceOptions) ? $rawChoiceOptions : (json_decode(is_string($rawChoiceOptions) && $rawChoiceOptions !== '' ? $rawChoiceOptions : '[]', true) ?: []);

        // ─── Colors Formatted ──────────────────────────────────────────────────
        $rawColors = $this->colors;
        $colors = is_array($rawColors) ? $rawColors : (json_decode(is_string($rawColors) && $rawColors !== '' ? $rawColors : '[]', true) ?: []);

        // ─── Related Products ──────────────────────────────────────────────────
        $relatedProducts = [];
        if (isset($this->related_products)) {
            foreach ($this->related_products as $related) {
                $relatedThumb = (string) $related->thumbnail;
                $relatedThumbUrl = ($relatedThumb === '' || $relatedThumb === 'def.png' || $relatedThumb === 'null')
                    ? null
                    : asset('storage/app/public/product/thumbnail/' . $relatedThumb);

                $relatedProducts[] = [
                    'id'             => $related->id,
                    'name'           => $related->name,
                    'slug'           => $related->slug,
                    'thumbnail'      => $relatedThumbUrl,
                    'unit_price'     => (float) $related->unit_price,
                    'purchase_price' => (float) $related->purchase_price,
                    'discount'       => (float) ($related->discount ?? 0),
                    'discount_type'  => $related->discount_type ?? 'percent',
                    'rating'         => (float) ($related->reviews_avg_rating ?? 0.0),
                    'reviews_count'  => (int) ($related->reviews_count ?? 0),
                ];
            }
        }

        $response = [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'product_type'      => $this->product_type ?? 'physical',
            'details'           => $this->details ?? '',
            'unit_price'        => (float) $this->unit_price,
            'purchase_price'    => (float) $this->purchase_price,
            'tax'               => (float) ($this->tax ?? 0),
            'tax_type'          => $this->tax_type ?? 'percent',
            'tax_model'         => $this->tax_model ?? 'exclude',
            'discount'          => (float) ($this->discount ?? 0),
            'discount_type'     => $this->discount_type ?? 'percent',
            'current_stock'     => (int) ($this->current_stock ?? 0),
            'minimum_order_qty' => (int) ($this->minimum_order_qty ?? 1),
            'thumbnail'         => $thumbnailUrl,
            'images'            => $images,
            'variation'         => $variation,
            'choice_options'    => $choiceOptions,
            'colors'            => $colors,
            'rating'            => (float) ($this->reviews_avg_rating ?? 0.0),
            'reviews_count'     => (int) ($this->reviews_count ?? 0),
            'wish_list_count'   => (int) ($this->wish_list_count ?? 0),
            'seller'            => $sellerData,
            'related_products'  => $relatedProducts,
        ];

        // Conditionally Exclude Nulls or Empty Arrays (like category)
        if ($this->relationLoaded('category') && $this->category) {
            $response['category'] = $this->category->name;
        }

        return $response;
    }
}
