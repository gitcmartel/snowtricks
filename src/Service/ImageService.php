<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    protected $uploadDirectory;

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
}