<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryThinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $icon = (string) $this->icon;
        $isDefault = ($icon === '' || $icon === 'def.png' || $icon === 'null');
        
        $iconUrl = null;
        if (!empty($this->icon_full_url)) {
            if (is_array($this->icon_full_url) && isset($this->icon_full_url['path'])) {
                $iconUrl = $this->icon_full_url['path'];
            } elseif (is_string($this->icon_full_url)) {
                $iconUrl = $this->icon_full_url;
            }
        }

        if (!$iconUrl) {
            $iconUrl = $isDefault ? null : asset('storage/app/public/category/' . $icon);
        }

        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $iconUrl,
        ];
    }
}
