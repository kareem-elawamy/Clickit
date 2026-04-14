<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $photo = (string) $this->photo;
        $isDefault = ($photo === '' || $photo === 'def.png' || $photo === 'null');
        
        $photoUrl = null;
        if (!empty($this->photo_full_url)) {
            if (is_array($this->photo_full_url) && isset($this->photo_full_url['path'])) {
                $photoUrl = $this->photo_full_url['path'];
            } elseif (is_string($this->photo_full_url)) {
                $photoUrl = $this->photo_full_url;
            }
        }

        if (!$photoUrl) {
            $photoUrl = $isDefault ? null : asset('storage/app/public/banner/' . $photo);
        }

        return [
            'id'    => $this->id,
            'photo' => $photoUrl,
            'url'   => $this->url,
        ];
    }
}
