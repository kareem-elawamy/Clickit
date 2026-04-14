<?php

namespace App\Http\Resources\RestAPI\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $image     = (string) $this->image;
        $isDefault = ($image === '' || $image === 'def.png' || $image === 'null');
        
        $imageUrl  = $isDefault 
            ? null 
            : asset('storage/app/public/brand/' . $image);

        return [
            'id'    => (int) $this->id,
            'name'  => (string) $this->name,
            'image' => $imageUrl,
        ];
    }
}
