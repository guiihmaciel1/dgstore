<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Concerns;

trait CompressesImages
{
    private function compressAndSaveImage(string $sourcePath, string $destinationPath, int $maxWidth = 1200, int $quality = 85): void
    {
        $dir = dirname($destinationPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $imageData = file_get_contents($sourcePath);
        $gdImage = @imagecreatefromstring($imageData);

        if ($gdImage === false) {
            copy($sourcePath, $destinationPath);

            return;
        }

        $width = imagesx($gdImage);
        $height = imagesy($gdImage);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($height * $maxWidth / $width);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $gdImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($gdImage);
            $gdImage = $resized;
        }

        imagejpeg($gdImage, $destinationPath, $quality);
        imagedestroy($gdImage);
    }
}
