<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaService
{
    private $uploadDirectory;

    public function __construct(string $uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * Move an uploaded file
     * @param UploadedFile $uploadedFile
     * @return string New name of the uploaded file
     */
    public function moveUploadedFile (UploadedFile $uploadedFile) : string 
    {
        $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move(
            $this->uploadDirectory,
            $newFilename
        );

        return $newFilename;
    }

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