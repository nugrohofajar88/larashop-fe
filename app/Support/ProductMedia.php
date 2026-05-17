<?php

namespace App\Support;

class ProductMedia
{
    public static function storefrontGallery(string $slug, string $primaryImage, string $category): array
    {
        return array_map(
            fn (array $image) => [
                'label' => $image['label'],
                'path' => $image['path'],
            ],
            self::definitions($slug, $primaryImage, $category)
        );
    }

    public static function adminGallery(string $slug, string $primaryImage, string $category, string $reference): array
    {
        return array_map(
            fn (array $image) => [
                'label' => $image['label'],
                'name' => "{$reference}-{$image['file_key']}.jpg",
                'is_primary' => $image['is_primary'],
                'path' => $image['path'],
            ],
            self::definitions($slug, $primaryImage, $category)
        );
    }

    private static function definitions(string $slug, string $primaryImage, string $category): array
    {
        $categoryInfoImage = $category === 'Benih'
            ? '/images/products/gallery-seed.svg'
            : '/images/products/gallery-field.svg';

        return [
            [
                'label' => 'Foto utama',
                'file_key' => 'cover',
                'is_primary' => true,
                'path' => $primaryImage,
            ],
            [
                'label' => 'Detail kemasan',
                'file_key' => 'packaging',
                'is_primary' => false,
                'path' => '/images/products/gallery-detail.svg',
            ],
            [
                'label' => 'Aplikasi produk',
                'file_key' => 'usage',
                'is_primary' => false,
                'path' => '/images/products/gallery-usage.svg',
            ],
            [
                'label' => self::categoryLabel($slug, $category),
                'file_key' => 'category-info',
                'is_primary' => false,
                'path' => $categoryInfoImage,
            ],
        ];
    }

    private static function categoryLabel(string $slug, string $category): string
    {
        return match ($slug) {
            'benih-cabai-f1', 'benih-tomat-unggul' => 'Info benih',
            'pestisida-organik', 'fungisida-cair' => 'Panduan aplikasi lapangan',
            default => $category === 'Benih' ? 'Info benih' : 'Info aplikasi lapangan',
        };
    }
}
