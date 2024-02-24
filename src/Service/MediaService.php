<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaService extends ImageService
{
   
    /**
     * Deletes a media if it's not the default image
     * @param string $mediaPath
     */
    public function deleteMedia(string $mediaPath) {
        $mediaName = basename($mediaPath);
        if ($mediaName !== 'hero_1.jpg' && $mediaName !== "" && file_exists($this->uploadDirectory . '\\' . $mediaName)) {
            unlink($this->uploadDirectory . '\\' . $mediaName);
        }
    }
}