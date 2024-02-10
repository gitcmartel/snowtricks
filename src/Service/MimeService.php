<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

class MimeService 
{
    private static $imageMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/svg'
    ];

    private static $videoMimeTypes = [
        'video/mp4'
    ];    
    /**
     * 
     */
    public static function getType(UploadedFile $uploadedFile): string {

        $fileMimeType = $uploadedFile->getMimeType();

        if (in_array($fileMimeType, self::$imageMimeTypes)) {
            return "image";
        } 
        // Vérifier si le type MIME correspond à une vidéo
        elseif (in_array($fileMimeType, self::$videoMimeTypes)) {
            return "video";
        }

        return '';
    }
}