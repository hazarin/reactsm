<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('one@one.com');
        $user->setName('User1');
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getName()
            )
        );
        $manager->persist($user);

        $user = new User();
        $user->setEmail('two@two.com');
        $user->setName('User2');
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getName()
            )
        );
        $manager->persist($user);

        $manager->flush();
    }
}
