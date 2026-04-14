<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * This resource is designed specifically for the product details screen.
     * It returns a lean, structured payload that eliminates:
     * - images_full_url bloat (replaced with a clean images array)
     * - color_images_full_url (replaced with colors_formatted)
     * - Heavy nested relationships (seller, category are trimmed to essentials)
     * - Null/empty fields converted to proper type defaults
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
        // Always include thumbnail as first image if gallery is empty
        if (empty($images)) {
            $images[] = $thumbnailUrl;
        }

        // ─── Seller & Shop ────────────────────────────────────────────────────
        $seller     = $this->whenLoaded('seller');
        $sellerData = null;
        if ($seller) {
            $shop = $seller->shop;
            $sellerData = [
                'id'        => $seller->id,
                'shop_id'   => $shop?->id,
                'shop_name' => $shop?->name ?? 'Admin',
            ];
        }

        // ─── Category ─────────────────────────────────────────────────────────
        $categoryName = $this->whenLoaded('category')?->name ?? null;

        // ─── Variations (cleaned) ─────────────────────────────────────────────
        $rawVariation = $this->variation;
        if (!is_array($rawVariation)) {
            $rawVariation = json_decode(is_string($rawVariation) && $rawVariation !== '' ? $rawVariation : '[]', true) ?: [];
        }
        $variation = array_map(fn($v) => [
            'type'  => $v['type']  ?? '',
            'price' => (float) ($v['price'] ?? 0),
            'sku'   => $v['sku']   ?? '',
            'qty'   => (int)   ($v['qty']   ?? 0),
        ], $rawVariation);

        // ─── Choice Options (cleaned) ──────────────────────────────────────────
        $rawChoiceOptions = $this->choice_options;
        if (!is_array($rawChoiceOptions)) {
            $rawChoiceOptions = json_decode(is_string($rawChoiceOptions) && $rawChoiceOptions !== '' ? $rawChoiceOptions : '[]', true) ?: [];
        }
        $choiceOptions = array_map(fn($opt) => [
            'name'    => $opt['name']    ?? '',
            'title'   => $opt['title']   ?? ($opt['name'] ?? ''),
            'options' => (array) ($opt['options'] ?? []),
        ], $rawChoiceOptions);

        // ─── Colors Formatted ──────────────────────────────────────────────────
        $rawColors = $this->colors;
        if (!is_array($rawColors)) {
            $rawColors = json_decode(is_string($rawColors) && $rawColors !== '' ? $rawColors : '[]', true) ?: [];
        }
        // Build color labels from codes — avoid DB hit by wrapping in static cache
        $colorsFormatted = [];
        if (!empty($rawColors)) {
            static $colorMap = null;
            if ($colorMap === null) {
                $colorMap = \App\Models\Color::pluck('name', 'code')->toArray();
            }
            foreach ($rawColors as $code) {
                $colorsFormatted[] = [
                    'name' => $colorMap[$code] ?? $code,
                    'code' => $code,
                ];
            }
        }

        // ─── Rating & Stock ────────────────────────────────────────────────────
        $rating        = (float) ($this->reviews_avg_rating ?? 0.0);
        $reviewsCount  = (int)   ($this->reviews_count      ?? 0);

        return [
            // ── Basics ─────────────────────────────────────────────────────────
            'id'               => $this->id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'code'             => $this->code ?? '',
            'product_type'     => $this->product_type ?? 'physical',

            // ── Pricing ────────────────────────────────────────────────────────
            'unit_price'       => (float) $this->unit_price,
            'purchase_price'   => (float) $this->purchase_price,
            'tax'              => (float) ($this->tax ?? 0),
            'tax_type'         => $this->tax_type ?? 'percent',
            'tax_model'        => $this->tax_model ?? 'exclude',
            'discount'         => (float) ($this->discount ?? 0),
            'discount_type'    => $this->discount_type ?? 'percent',

            // ── Stock & Shipping ────────────────────────────────────────────────
            'current_stock'    => (int) ($this->current_stock    ?? 0),
            'minimum_order_qty'=> (int) ($this->minimum_order_qty ?? 1),
            'free_shipping'    => (int) ($this->free_shipping     ?? 0),

            // ── Media ──────────────────────────────────────────────────────────
            'thumbnail'        => $thumbnailUrl,
            'images'           => $images,

            // ── Description ────────────────────────────────────────────────────
            'details'          => $this->details ?? '',

            // ── Variants & Options ─────────────────────────────────────────────
            'variation'        => $variation,
            'choice_options'   => $choiceOptions,
            'colors'           => $rawColors,
            'colors_formatted' => $colorsFormatted,

            // ── Relations ──────────────────────────────────────────────────────
            'seller'           => $sellerData,
            'category'         => $categoryName,

            // ── Rating ─────────────────────────────────────────────────────────
            'rating'           => $rating,
            'reviews_count'    => $reviewsCount,

            // ── Meta ───────────────────────────────────────────────────────────
            'meta_title'       => $this->meta_title       ?? '',
            'meta_description' => $this->meta_description ?? '',
        ];
    }
}
