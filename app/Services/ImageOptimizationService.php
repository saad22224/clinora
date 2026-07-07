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
    public function upload(UploadedFile $file, string $folder, ?int $width = null, ?int $height = null): string
    {
        // Generate a random filename starting with 'item-'
        $filename = 'item-'.Str::random(20).'.webp';
        $path = $folder.'/'.$filename;

        // Create manager using GD driver
        $manager = new ImageManager(new Driver);

        // Read image data as binary content to avoid permission issues with Windows Temp folder
        if (method_exists($manager, 'decodeBinary')) {
            $image = $manager->decodeBinary($file->get()); // V4
        } else {
            $image = $manager->read($file->get()); // V3
        }

        // Scale image if dimensions are provided
        if ($width || $height) {
            $image->scale(width: $width, height: $height);
        }

        // Encode using WebP format
        $encoded = $image->encode(new WebpEncoder(quality: 80));

        // Store the encoded image in the public disk
        Storage::disk('public')->put($path, (string) $encoded);

        Log::info('Service Upload - Path: '.$path);

        // Return the full relative path inside the public disk so Storage::url($path) resolves correctly
        return (string) $path;
    }
}
