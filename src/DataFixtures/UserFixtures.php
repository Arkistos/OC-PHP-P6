<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasherInterface
    ) {
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        for ($fakeUser = 1; $fakeUser <= 5; ++$fakeUser) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setUsername($faker->name());
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword($user, 'testing')
            );
            $manager->persist($user);
            $this->addReference($fakeUser, $user);
        }
        $manager->flush();
    }
}
