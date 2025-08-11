<?php

namespace App\Traits;

trait ImageHandler
{
    /**
     * Convert uploaded image to base64
     */
    protected function convertImageToBase64($imageFile)
    {
        if (!$imageFile) {
            return [null, null];
        }

        $imageData = file_get_contents($imageFile->getRealPath());
        $base64Image = base64_encode($imageData);
        $mimeType = $imageFile->getMimeType();

        return [$base64Image, $mimeType];
    }

    /**
     * Validate image file
     */
    protected function validateImage($imageFile)
    {
        return $imageFile && $imageFile->isValid() && 
               in_array($imageFile->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']) &&
               $imageFile->getSize() <= 5120 * 1024; // 5MB max
    }

    /**
     * Get base64 image data URI
     */
    protected function getBase64DataUri($base64Data, $mimeType)
    {
        if ($base64Data && $mimeType) {
            return 'data:' . $mimeType . ';base64,' . $base64Data;
        }
        return null;
    }
}
