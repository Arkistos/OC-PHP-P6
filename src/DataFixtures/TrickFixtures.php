<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $sluggerInterface)
    {}

    public function load(ObjectManager $manager): void
    {
        //$product = new Product();
        // $manager->persist($product);
        $this->createTrick(
            'Mute', 
            'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
            ['Grabs'],
            $manager
        );
        $this->createTrick(
            'Indy', 
            'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
            ['Grabs'],
            $manager
        );
        $this->createTrick(
            '360°', 
            'Tour complet.',
            ['Rotations'],
            $manager
        );
        $this->createTrick(
            '180° BackFlip', 
            'Demi tour avec salto arrière.',
            ['Rotations', 'Flips'],
            $manager
        );
        $this->createTrick(
            'Nose Slide', 
            "Glisse sur une barre avec la plance penché vers l'avant",
            ['Slides'],
            $manager
        );
        $this->createTrick(
            'Tail Slide', 
            "Glisse sur une barre avec la plance penché vers l'arrière",
            ['Slides'],
            $manager
        );
        $this->createTrick(
            'Front Flip', 
            'Salto avant',
            ['Flips'],
            $manager
        );

        $manager->flush();
    }

    private function createTrick(string $name, string $description, array $groupReferences, ObjectManager $manager):void
    {
        $trick = new Trick();
        $trick->setName($name);
        $trick->setSlug($this->sluggerInterface->slug($name)->lower());
        $trick->setDescription($description);

        foreach($groupReferences as $groupReference){
            $group = $this->getReference($groupReference);
            $trick->addGroup($group);
        }
        $manager->persist($trick);

        $this->addReference($trick->getSlug(), $trick);
    } 

    public function getDependencies()
    {
        return[
            GroupFixtures::class
        ];
    }
}
