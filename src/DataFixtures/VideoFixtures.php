<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createVideo(
            'k6aOWf0LDcQ',
            'mute',
            $manager
        );
        $this->createVideo(
            '6yA3XqjTh_w',
            'indy',
            $manager
        );
        $this->createVideo(
            'grXpguVaqls',
            '3600',
            $manager
        );
        $this->createVideo(
            'Sj7CJH9YvAo',
            '1800-backflip',
            $manager
        );
        $this->createVideo(
            'oAK9mK7wWvw',
            'nose-slide',
            $manager
        );
        $this->createVideo(
            'HRNXjMBakwM',
            'tail-slide',
            $manager
        );
        $this->createVideo(
            'gMfmjr-kuOg',
            'front-flip',
            $manager
        );
        $this->createVideo(
            'k-CoAquRSwY',
            'mctwist',
            $manager
        );
        $this->createVideo(
            'rH1cfVY4qgc',
            'switch-to-rails',
            $manager
        );
        $this->createVideo(
            'QX6yvs6uTVg',
            'backside-rodeo',
            $manager
        );

        $manager->flush();
    }

    private function createVideo(string $link, string $trickSlug, ObjectManager $objectManager): void
    {
        $video = new Video();
        $video->setLink($link);
        $video->setTrick($this->getReference($trickSlug));
        $objectManager->persist($video);
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
