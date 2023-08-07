<?php

namespace App\Service;

class VideoService
{
    public function getLinks(?string $links): string
    {
        if(preg_match_all('/https:\/\/youtu.be\/(?<link>[\w\-]{11})/', $links, $matches)) {
            return $matches['link'][0];

        }

        return '';
    }
}
