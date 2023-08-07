<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createGroup('Grabs', $manager);
        $this->createGroup('Rotations', $manager);
        $this->createGroup('Flips', $manager);
        $this->createGroup('Slides', $manager);

        $manager->flush();
    }

    private function createGroup(string $groupeName, ObjectManager $manager): void
    {
        $group = new Group();
        $group->setName($groupeName);
        $manager->persist($group);

        $this->addReference($group->getName(), $group);
    }
}
