<?php

namespace App\Service;

class VideoService
{
    public function getLinks(?string $links):array
    {
        if(preg_match_all('/https:\/\/youtu.be\/(?<link>[\w\-]{11})/',$links,$matches)){
            return $matches['link'];
        }
        

        return [];
    }
}