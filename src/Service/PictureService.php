<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, string $name, ?string $folder='', ?int $width = 250, ?int $height=250)
    {
        $fichier = $name. '.webp';

        $pictureInfos = getimagesize($picture);
        if($pictureInfos === false) {
            throw new Exception('Format d\'image incorect');
        }

        switch($pictureInfos['mime']) {
            case 'image/png':
                $pictureSource = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $pictureSource = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $pictureSource = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception('Fromat d\'image incorrect');
        }

        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        switch($imageWidth <=> $imageHeight) {
            case -1:
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = ($imageHeight-$squareSize)/2;
                break;
            case 0:
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = 0;
                break;
            case 1:
                $squareSize = $imageHeight;
                $srcX = ($imageWidth-$squareSize)/2;
                $srcY = 0;
                break;
        }

        $resizedPicture = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizedPicture, $pictureSource, 0, 0, $srcX, $srcY, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory').$folder;

        imagewebp($resizedPicture, $path.'/'.$fichier);
        //$picture->move($path .'/', $fichier);

        return $fichier;
    }

    public function delete(string $fichier, ?string $folder='', ?int $width = 250, ?int $height=250)
    {
        if($fichier !== 'default.webp') {
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $fichier;
            if(file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;
            if(file_exists($original)) {
                unlink($original);
                $success = true;
            }
            return $success;
        }
        return false;
    }

}
