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
        $user
            ->setEmail('one@one.com')
            ->setName('User1')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getName()
                )
            );
        $manager->persist($user);

        $user = new User();
        $user
            ->setEmail('two@two.com')
            ->setName('User2')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getName()
                )
            );
        $manager->persist($user);

        $user = new User();
        $user
            ->setEmail('admin@admin.com')
            ->setName('Admin')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getName()
                )
            )
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
