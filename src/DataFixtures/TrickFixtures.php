<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $sluggerInterface)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->createTrick(
            'Mute',
            'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
            ['Grabs'],
            [33, 34],
            263,
            $manager
        );
        $this->createTrick(
            'Indy',
            'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
            ['Grabs'],
            [35, 36, 37],
            264,
            $manager
        );
        $this->createTrick(
            '360°',
            'Tour complet.',
            ['Rotations'],
            [38],
            265,
            $manager
        );
        $this->createTrick(
            '180° BackFlip',
            'Demi tour avec salto arrière.',
            ['Rotations', 'Flips'],
            [39],
            266,
            $manager
        );
        $this->createTrick(
            'Nose Slide',
            "Glisse sur une barre avec la plance penché vers l'avant",
            ['Slides'],
            [40, 41],
            267,
            $manager
        );
        $this->createTrick(
            'Tail Slide',
            "Glisse sur une barre avec la plance penché vers l'arrière",
            ['Slides'],
            [42],
            268,
            $manager
        );
        $this->createTrick(
            'Front Flip',
            'Salto avant',
            ['Flips'],
            [43, 44],
            269,
            $manager
        );
        
        $manager->flush();
    }

    private function createTrick(string $name, string $description, array $groupReferences, array $picturesId, int $id, ObjectManager $manager): void
    {
        $trick = new Trick();
        $trick->setName($name);
        $trick->setSlug($this->sluggerInterface->slug($name)->lower());
        $trick->setDescription($description);
        $trick->setCreatedAt(new \DateTimeImmutable());

        foreach ($groupReferences as $groupReference) {
            $group = $this->getReference($groupReference);
            $trick->addGroup($group);
        }

        foreach ($picturesId as $index => $pictureId) {
            $picture = new Picture();
            $picture->setTrick($trick);

            $manager->persist($picture);
            
        }

        $manager->persist($trick);

        
        $this->addReference($trick->getSlug(), $trick);
        
    }

    public function getDependencies()
    {
        return [
            GroupFixtures::class,
        ];
    }
}
