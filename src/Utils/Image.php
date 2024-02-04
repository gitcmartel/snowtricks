<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image
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
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move(
            $this->uploadDirectory,
            $newFilename
        );

        return $newFilename;
    }
}