<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
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
     * Deletes an image if it's not the default one
     * @param string $imagePath
     */
    public function deleteImage(string $imagePath) {
        $imageName = basename($imagePath);
        if ($imageName !== 'hero_1.jpg') {
            unlink($this->uploadDirectory . '\\' . $imageName);
        }
    }
}