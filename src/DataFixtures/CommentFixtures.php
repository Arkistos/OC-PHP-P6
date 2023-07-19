<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createComment(
            'mute',
            $manager
        );
        $this->createComment(
            'indy',
            $manager
        );
        $this->createComment(
            '3600',
            $manager
        );
        $this->createComment(
            '1800-backflip',
            $manager
        );
        $this->createComment(
            'nose-slide',
            $manager
        );
        $this->createComment(
            'tail-slide',
            $manager
        );
        $this->createComment(
            'front-flip',
            $manager
        );

        $manager->flush();

        

        
    }

    private function createComment(string $trickSlug, ObjectManager $manager):Comment
    {
        $comment = new Comment();

        $faker = Faker\Factory::create('fr_FR');

        $comment->setContent($faker->paragraph());
        $comment->setCreatedAt(new DateTimeImmutable());
        $comment->setTrick($this->getReference($trickSlug));
        $comment->setUser($this->getReference($faker->numberBetween(1,5)));
        $manager->persist($comment);
        return $comment;
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class
        ];
    }
}