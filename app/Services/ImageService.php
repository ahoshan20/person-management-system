<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    public function validateAndStore(UploadedFile $file): array
    {
        // Type check
        $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowed)) {
            throw new \Exception('Photo must be JPG/JPEG/PNG format.');
        }

        // Size check (2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            throw new \Exception('Photo must not exceed 2MB.');
        }

        // Dimension check
        [$width, $height] = getimagesize($file->getRealPath());
        if ($width !== 600 || $height !== 600) {
            throw new \Exception('Photo must be exactly 600x600 pixels.');
        }

        $name = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store original
        $file->storeAs('public/photos', $name);

        // Generate 150x150 thumb
        $manager = new ImageManager(new Driver());
        $thumb = $manager->read($file->getRealPath())
                         ->resize(150, 150);
        $thumb->save(storage_path("app/public/thumbs/{$name}"));

        return ['photo' => $name, 'thumb' => $name];
    }

    public function delete(string $name): void
    {
        Storage::delete("public/photos/{$name}");
        Storage::delete("public/thumbs/{$name}");
    }
}