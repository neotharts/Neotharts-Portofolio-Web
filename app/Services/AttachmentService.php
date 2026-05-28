<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AttachmentService
{
    protected $imageManager;

    // Allowed file types
    protected $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    protected $allowedDocTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
    protected $maxFileSize = 10 * 1024 * 1024; // 10MB
    protected $maxImages = 5;
    protected $maxDocs = 3;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Process and store attachments
     *
     * @param array $files
     * @return array
     */
    public function processAttachments(array $files): array
    {
        $results = [];
        $imageCount = 0;
        $docCount = 0;

        foreach ($files as $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }

            // Check file size
            if ($file->getSize() > $this->maxFileSize) {
                $results[] = [
                    'success' => false,
                    'name' => $file->getClientOriginalName(),
                    'error' => 'File size exceeds 10MB limit',
                ];
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());

            // Check if it's an image
            if (in_array($extension, $this->allowedImageTypes)) {
                if ($imageCount >= $this->maxImages) {
                    $results[] = [
                        'success' => false,
                        'name' => $file->getClientOriginalName(),
                        'error' => 'Maximum ' . $this->maxImages . ' images allowed',
                    ];
                    continue;
                }

                $result = $this->processImage($file);
                if ($result['success']) {
                    $imageCount++;
                }
                $results[] = $result;
            }
            // Check if it's a document
            elseif (in_array($extension, $this->allowedDocTypes)) {
                if ($docCount >= $this->maxDocs) {
                    $results[] = [
                        'success' => false,
                        'name' => $file->getClientOriginalName(),
                        'error' => 'Maximum ' . $this->maxDocs . ' documents allowed',
                    ];
                    continue;
                }

                $result = $this->processDocument($file);
                if ($result['success']) {
                    $docCount++;
                }
                $results[] = $result;
            }
            else {
                $results[] = [
                    'success' => false,
                    'name' => $file->getClientOriginalName(),
                    'error' => 'File type not supported',
                ];
            }
        }

        return $results;
    }

    /**
     * Process and compress image
     *
     * @param UploadedFile $file
     * @return array
     */
    protected function processImage(UploadedFile $file): array
    {
        try {
            $originalSize = $file->getSize();
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());

            // Generate filename
            $newFilename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
                . '-' . Str::random(8)
                . '.webp';

            // Get original dimensions
            $image = $this->imageManager->read($file->getPathname());
            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // Calculate new dimensions (max 1920px)
            $maxSize = 1920;
            if ($originalWidth > $maxSize || $originalHeight > $maxSize) {
                $ratio = min($maxSize / $originalWidth, $maxSize / $originalHeight);
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);
                $image->resize($newWidth, $newHeight);
            }

            // Encode to WebP with quality 80
            $encoded = $image->encode('webp', 80);

            // Save to temp file first
            $tempPath = storage_path('app/temp_' . $newFilename);
            file_put_contents($tempPath, $encoded);

            // Calculate compression
            $compressedSize = filesize($tempPath);
            $savedPercentage = round((1 - ($compressedSize / $originalSize)) * 100, 1);

            // Store
            $storagePath = 'messages/' . date('Y/m') . '/' . $newFilename;
            Storage::disk('public')->put($storagePath, file_get_contents($tempPath));

            // Clean up
            @unlink($tempPath);

            return [
                'success' => true,
                'name' => $originalName,
                'type' => 'image',
                'path' => $storagePath,
                'original_size' => $originalSize,
                'compressed_size' => $compressedSize,
                'saved_percentage' => $savedPercentage,
                'dimensions' => [
                    'width' => $image->width(),
                    'height' => $image->height(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process document (no compression, just store)
     *
     * @param UploadedFile $file
     * @return array
     */
    protected function processDocument(UploadedFile $file): array
    {
        try {
            $originalSize = $file->getSize();
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());

            // Generate filename
            $newFilename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
                . '-' . Str::random(8)
                . '.' . $extension;

            // Store directly
            $storagePath = 'messages/' . date('Y/m') . '/' . $newFilename;
            $file->storeAs('public/' . dirname($storagePath), basename($storagePath));

            // Actually use storeAs properly
            $path = $file->storeAs(
                dirname($storagePath),
                basename($storagePath),
                'public'
            );

            return [
                'success' => true,
                'name' => $originalName,
                'type' => 'document',
                'path' => $path,
                'original_size' => $originalSize,
                'extension' => $extension,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete attachment file
     *
     * @param string $path
     * @return bool
     */
    public function deleteAttachment(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Get file icon based on type
     *
     * @param string $extension
     * @return string
     */
    public function getFileIcon(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => 'picture_as_pdf',
            'doc', 'docx' => 'description',
            'xls', 'xlsx' => 'table_chart',
            'ppt', 'pptx' => 'slideshow',
            'txt', 'rtf' => 'article',
            default => 'insert_drive_file',
        };
    }

    /**
     * Format file size for display
     *
     * @param int $bytes
     * @return string
     */
    public function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}