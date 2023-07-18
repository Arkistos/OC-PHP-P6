<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->makeVideo(
            'k6aOWf0LDcQ',
            'mute',
            $manager
        );
        $this->makeVideo(
            '6yA3XqjTh_w',
            'indy',
            $manager
        );
        $this->makeVideo(
            'grXpguVaqls',
            '3600',
            $manager
        );
        $this->makeVideo(
            'Sj7CJH9YvAo',
            '1800-backflip',
            $manager
        );
        $this->makeVideo(
            'oAK9mK7wWvw',
            'nose-slide',
            $manager
        );
        $this->makeVideo(
            'HRNXjMBakwM',
            'tail-slide',
            $manager
        );
        $this->makeVideo(
            'gMfmjr-kuOg',
            'front-flip',
            $manager
        );

        $manager->flush();
    }

    private function makeVideo(string $link, string $trickSlug, ObjectManager $objectManager):void
    {
        $video = new Video();
        $video->setLink($link);
        $video->setTrick($this->getReference($trickSlug));
        $objectManager->persist($video);
    }
}
