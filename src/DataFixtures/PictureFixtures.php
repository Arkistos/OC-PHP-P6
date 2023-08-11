<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureFixtures extends Fixture implements DependentFixtureInterface
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager): void
    {
        $this->movePictures('mute');
        $this->movePictures('indy');
        $this->movePictures('3600');
        $this->movePictures('1800-backflip');
        $this->movePictures('nose-slide');
        $this->movePictures('tail-slide');
        $this->movePictures('front-flip');

        $manager->flush();
    }

    public function movePictures(string $trickSlug): void
    {
        $trick = $this->getReference($trickSlug);
        foreach ($trick->getPictures() as $index => $picture) {
            $oldPath = $path = $this->params->get('ressources_directory').$trick->getSlug().$index.'.webp';
            $newPath = $path = $this->params->get('images_directory').'tricks_pictures/'.$trick->getId().'-'.$picture->getId().'.webp';
            copy($oldPath, $newPath);
        }
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
