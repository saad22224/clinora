<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageOptimizationService
{
    /**
     * Upload and optimize image to WebP format (Intervention Image V3/V4).
     */
    public function upload(UploadedFile $file, string $folder, ?int $width = null, ?int $height = null, string $disk = 'public'): string
    {
        $filename = 'item-'.Str::random(20).'.webp';
        $path = $folder.'/'.$filename;

        $manager = new ImageManager(new Driver);

        if (method_exists($manager, 'decodeBinary')) {
            $image = $manager->decodeBinary($file->get());
        } else {
            $image = $manager->read($file->get());
        }

        if ($width || $height) {
            $image->scale(width: $width, height: $height);
        }

        $encoded = $image->encode(new WebpEncoder(quality: 80));

        Storage::disk($disk)->put($path, (string) $encoded);

        Log::info('Service Upload - Path: '.$path.' Disk: '.$disk);

        return (string) $path;
    }
}
