<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $icon = (string) $this->icon;
        $isDefault = ($icon === '' || $icon === 'def.png' || $icon === 'null');
        $iconUrl   = $isDefault 
            ? null 
            : asset('storage/app/public/category/' . $icon);

        return [
            'id'            => (int) $this->id,
            'name'          => (string) $this->name,
            'icon'          => $iconUrl,
            'product_count' => (int) ($this->product_count ?? 0),
        ];
    }
}
