<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;

class ProductImageService
{
    use FileManagerTrait;

    public function getProcessedImages(object $request): array
    {
        $colorImageSerial = [];
        $imageNames = [];
        $storage = config('filesystems.disks.default') ?? 'public';
        if ($request->has('colors_active') && $request->has('colors') && count($request['colors']) > 0) {
            foreach ($request['colors'] as $color) {
                $color_ = Str::replace('#', '', $color);
                $img = 'color_image_' . $color_;
                if ($request->file($img)) {
                    $image = $this->upload(dir: 'product/', format: 'webp', image: $request->file($img));
                    $colorImageSerial[] = [
                        'color' => $color_,
                        'image_name' => $image,
                        'storage' => $storage,
                    ];
                    $imageNames[] = [
                        'image_name' => $image,
                        'storage' => $storage,
                    ];
                } else if ($request->has($img)) {
                    $image = $request->$img[0];
                    $colorImageSerial[] = [
                        'color' => $color_,
                        'image_name' => $image,
                        'storage' => $storage,
                    ];
                    $imageNames[] = [
                        'image_name' => $image,
                        'storage' => $storage,
                    ];
                }
            }
        }
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'product/', format: 'webp', image: $image);
                $imageNames[] = [
                    'image_name' => $images,
                    'storage' => $storage,
                ];
                if ($request->has('colors_active') && $request->has('colors') && count($request['colors']) > 0) {
                    $colorImageSerial[] = [
                        'color' => null,
                        'image_name' => $images,
                        'storage' => $storage,
                    ];
                }
            }
        }
        if (!empty($request->existing_images)) {
            foreach ($request->existing_images as $image) {
                $colorImageSerial[] = [
                    'color' => null,
                    'image_name' => $image,
                    'storage' => $storage,
                ];

                $imageNames[] = [
                    'image_name' => $image,
                    'storage' => $storage,
                ];
            }
        }
        return [
            'image_names' => $imageNames ?? [],
            'colored_image_names' => $colorImageSerial ?? []
        ];

    }

    public function getProcessedUpdateImages(object $request, object $product): array
    {
        $productImages = collect(json_decode($product->images, true))
            ->unique('image_name')
            ->values()->toArray();

        $colorImageArray = [];
        $storage = config('filesystems.disks.default') ?? 'public';
        $dbColorImage = $product->color_image ? json_decode($product->color_image, true) : [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            if (!$dbColorImage) {
                foreach ($productImages as $image) {
                    $image = is_string($image) ? $image : (array)$image;
                    $dbColorImage[] = [
                        'color' => null,
                        'image_name' => is_array($image) ? $image['image_name'] : $image,
                        'storage' => $image['storage'] ?? $storage,
                    ];
                }
            }

            $dbColorImageFinal = [];
            if ($dbColorImage) {
                foreach ($dbColorImage as $colorImage) {
                    if ($colorImage['color']) {
                        $dbColorImageFinal[] = $colorImage['color'];
                    }
                }
            }

            $inputColors = [];
            foreach ($request->colors as $color) {
                $inputColors[] = str_replace('#', '', $color);
            }
            $colorImageArray = $dbColorImage;

            foreach ($inputColors as $color) {
                $image = 'color_image_' . $color;
                if (!in_array($color, $dbColorImageFinal)) {
                    if ($request->file($image)) {
                        $imageName = $this->upload(dir: 'product/', format: 'webp', image: $request->file($image));
                        $productImages[] = [
                            'image_name' => $imageName,
                            'storage' => $storage,
                        ];
                        $colorImages = [
                            'color' => $color,
                            'image_name' => $imageName,
                            'storage' => $storage,
                        ];
                        $colorImageArray[] = $colorImages;
                    }
                } else if ($dbColorImage && in_array($color, $dbColorImageFinal) && $request->has($image) && $request->file($image)) {
                    $dbColorFilterImages = [];
                    foreach ($dbColorImage as $colorImage) {
                        if ($colorImage['color'] == $color) {
                            $this->delete(filePath: 'product/' . $colorImage['image_name']);
                            $imageName = $this->upload(dir: 'product/', format: 'webp', image: $request->file($image));

                            $productImages = collect($productImages)->filter(function ($productImageItem) use ($colorImage) {
                                if (is_array($productImageItem) && isset($productImageItem['image_name'])) {
                                    return $productImageItem['image_name'] != $colorImage['image_name'];
                                }
                                return $productImageItem != $colorImage['image_name'];
                            })->values()->toArray();


                            $dbColorFilterImages = collect($dbColorImage)->filter(function ($dbColorImageItem) use ($colorImage) {
                                return $dbColorImageItem['image_name'] != $colorImage['image_name'];
                            })->values()->toArray();

                            $productImages[] = [
                                'image_name' => $imageName,
                                'storage' => $storage,
                            ];

                            $colorImageArray = collect($colorImageArray)->filter(function ($colorItem) use ($color, $colorImage) {
                                return $colorItem['color'] != $color && $colorItem['image_name'] != $colorImage['image_name'];
                            })->values()->toArray();

                            $colorImages = [
                                'color' => $color,
                                'image_name' => $imageName,
                                'storage' => $storage,
                            ];
                            $colorImageArray[] = $colorImages;
                        }
                    }
                    $dbColorImage = $dbColorFilterImages;
                }
            }
        }

        foreach ($dbColorImage as $colorImage) {
            $image = is_string($colorImage) ? $colorImage : (array)$colorImage;
            $productImages[] = [
                'image_name' => is_array($image) ? $image['image_name'] : $image,
                'storage' => $image['storage'] ?? $storage,
            ];
        }
        $requestColors = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            foreach ($request['colors'] as $color) {
                $requestColors[] = str_replace('#', '', $color);
            }
        }

        foreach ($colorImageArray as $colorImage) {
            if (!in_array($colorImage['color'], $requestColors)) {
                $productImages[] = [
                    'image_name' => $colorImage['image_name'],
                    'storage' => $colorImage['storage'] ?? $storage,
                ];
            }
        }

        $colorImageArray = collect($colorImageArray)->map(function ($colorImage) use ($requestColors) {
            if (!in_array($colorImage['color'], $requestColors)) {
                $colorImage['color'] = null;
            }
            return $colorImage;
        })->sortByDesc(function ($colorImage) {
            return !is_null($colorImage['color']);
        })->values()->toArray();

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $this->upload(dir: 'product/', format: 'webp', image: $image);
                $productImages[] = [
                    'image_name' => $imageName,
                    'storage' => $storage,
                ];
                if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                    $colorImageArray[] = [
                        'color' => null,
                        'image_name' => $imageName,
                        'storage' => $storage,
                    ];
                }
            }
        }
        $productImages = collect($productImages)->unique('image_name')->values()->toArray();

        return [
            'image_names' => $productImages ?? [],
            'colored_image_names' => $colorImageArray ?? []
        ];
    }

    public function deleteImages(object $product): bool
    {
        foreach (json_decode($product['images'], true) as $image) {
            $this->delete(filePath: '/product/' . (isset($image['image_name']) ? $image['image_name'] : $image));
        }
        $this->delete(filePath: '/product/thumbnail/' . $product['thumbnail']);

        return true;
    }

    public function deletePreviewFile(object $product): bool
    {
        if ($product['preview_file']) {
            $this->delete(filePath: '/product/preview/' . $product['preview_file']);
        }
        return true;
    }

    public function deleteImage(object $request, object $product): array
    {
        $colors = json_decode($product['colors']);
        $color_image = json_decode($product['color_image']);
        $images = [];
        $imageNames = [];
        $color_images = [];
        if ($colors && $color_image) {
            foreach ($color_image as $img) {
                if ($img->color != $request['color'] && $img?->image_name != $request['name']) {
                    $imageNames[] = $img->image_name;
                    $color_images[] = [
                        'color' => $img->color != null ? $img->color : null,
                        'image_name' => $img->image_name,
                        'storage' => $img?->storage ?? 'public',
                    ];
                }
            }

            foreach (json_decode($product['images']) as $image) {
                $imageName = $image?->image_name ?? $image;
                if ($imageName != $request['name'] && !in_array($imageName, $imageNames)) {
                    $color_images[] = [
                        'color' => null,
                        'image_name' => $imageName,
                        'storage' => $image?->storage ?? 'public',
                    ];
                }
            }
        }

        foreach (json_decode($product['images']) as $image) {
            $imageName = $image?->image_name ?? $image;
            if ($imageName != $request['name']) {
                $images[] = $image;
            }
        }

        return [
            'images' => $images,
            'color_images' => $color_images
        ];
    }
}
