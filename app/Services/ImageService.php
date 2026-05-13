<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        // Create manager with GD driver
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Compress and resize image
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     */
    public function compress(UploadedFile $file, array $options = [])
    {
        // Default settings
        $defaults = [
            'max_width' => 1920,      // Max width in pixels
            'max_height' => 1920,     // Max height in pixels
            'quality' => 85,           // JPEG quality (1-100)
            'format' => 'jpeg',        // Output format: jpeg, webp, png
            'suffix' => '_compressed', // Filename suffix
        ];

        $settings = array_merge($defaults, $options);

        // Get original file info
        $originalSize = $file->getSize();
        $originalName = $file->getClientOriginalName();

        // Generate new filename
        $newFilename = pathinfo($originalName, PATHINFO_FILENAME)
            . $settings['suffix']
            . '.' . $settings['format'];

        // Read and process image using decode() method
        $image = $this->manager->decodePath($file->getPathname());

        // Get current dimensions
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Calculate new dimensions maintaining aspect ratio
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;

        if ($originalWidth > $settings['max_width'] || $originalHeight > $settings['max_height']) {
            $ratio = min(
                $settings['max_width'] / $originalWidth,
                $settings['max_height'] / $originalHeight
            );
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
        }

        // Resize image
        $image->resize($newWidth, $newHeight);

        // Encode to desired format with quality
        $encoder = $this->getEncoder($settings['format'], $settings['quality']);
        $encoded = $image->encode($encoder);

        // Save to temp file first to measure size
        $tempPath = storage_path('app/temp_' . $newFilename);
        file_put_contents($tempPath, $encoded);

        // Get compressed size
        $compressedSize = filesize($tempPath);

        // Generate storage path
        $storagePath = 'artworks/' . $newFilename;

        // Store the file
        Storage::disk('public')->put($storagePath, file_get_contents($tempPath));

        // Clean up temp file
        @unlink($tempPath);

        return [
            'path' => $storagePath,
            'original_size' => $originalSize,
            'compressed_size' => $compressedSize,
            'saved_percentage' => round((1 - ($compressedSize / $originalSize)) * 100, 1),
            'original_name' => $originalName,
            'new_name' => $newFilename,
            'dimensions' => [
                'width' => $newWidth,
                'height' => $newHeight,
            ],
        ];
    }

    /**
     * Get encoder based on format
     */
    private function getEncoder(string $format, int $quality)
    {
        return match (strtolower($format)) {
            'jpeg', 'jpg' => new JpegEncoder(quality: $quality),
            'png' => new PngEncoder(),
            'webp' => new WebpEncoder(quality: $quality),
            default => new JpegEncoder(quality: $quality),
        };
    }

    /**
     * Batch compress multiple images
     *
     * @param array $files
     * @param array $options
     * @return array
     */
    public function batchCompress(array $files, array $options = [])
    {
        $results = [];

        foreach ($files as $file) {
            try {
                $results[] = [
                    'success' => true,
                    'data' => $this->compress($file, $options),
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get image info without processing
     *
     * @param UploadedFile $file
     * @return array
     */
    public function getInfo(UploadedFile $file)
    {
        $image = $this->manager->decodePath($file->getPathname());

        return [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'dimensions' => [
                'width' => $image->width(),
                'height' => $image->height(),
            ],
        ];
    }
}