<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
      $user = new User();
        $user->setUsername('usuario')
            ->setPassword('$2y$13$UB7EjG7zVoCQwHObG1F.1.wkLGffWwnBn2IEI65E/hckA2ljYpoMW
');
        $manager->persist($user);
        $manager->flush();
    }
}
