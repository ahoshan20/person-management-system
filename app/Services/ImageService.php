<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::usingDriver(Driver::class);
    }

    public function validateAndStore(UploadedFile $file): array
    {
        $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowed)) {
            throw new \Exception('Photo must be JPG/JPEG/PNG format.');
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            throw new \Exception('Photo must not exceed 2MB.');
        }

        $imageInfo = getimagesize($file->getRealPath());
        if (!$imageInfo) {
            throw new \Exception('Could not read the image file.');
        }

        [$width, $height] = $imageInfo;
        if ($width !== 600 || $height !== 600) {
            throw new \Exception('Photo must be exactly 600x600 pixels.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $name      = Str::uuid() . '.' . $extension;
        
        $file->storeAs('photos', $name, 'public');

        // Generate thumb
        $thumb = $this->manager
            ->decodePath($file->getRealPath())
            ->cover(150, 150);   // crop center to exact 150x150

        // Save thumb to storage
        $thumbPath = storage_path('app/public/thumbs/' . $name);
        $thumb->save($thumbPath);

        return [
            'photo' => $name,
            'thumb' => $name,
        ];
    }

    public function delete(string $name): void
    {
        Storage::disk('public')->delete('photos/' . $name);
        Storage::disk('public')->delete('thumbs/' . $name);
    }
}