<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * Upload single file
     */
    public static function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        return $file->store($folder, 'public');
    }

    /**
     * Upload multiple files
     */
    public static function uploadMultiple(array $files, string $folder = 'uploads'): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = self::upload($file, $folder);
            }
        }

        return $paths;
    }

    /**
     * Delete single file
     */
    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Delete multiple files
     */
    public static function deleteMultiple(array $paths): void
    {
        foreach ($paths as $path) {
            self::delete($path);
        }
    }

    /**
     * Replace file (delete old + upload new)
     */
    public static function replace(
        ?string $oldPath,
        UploadedFile $newFile,
        string $folder = 'uploads'
    ): string {
        self::delete($oldPath);
        return self::upload($newFile, $folder);
    }

    private function deleteFileIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
