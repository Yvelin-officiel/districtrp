<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i <= 50; $i++){
            $vehicle = new Vehicle;
            $vehicle->setName('Vehicle ' .  $i)
                ->setPrice(mt_rand(100, 1000))
                -> setImage('public\images\uploads\vehicle\Look-at-me-648713d86c46d.png');
                

            $manager->persist($vehicle);
        }

        $user = new User();
        
        $user->setUsername('user')
        ->setRoles(['ROLE_USER'])
        ->setPlainpassword('password');

        $manager->persist($user);

        $manager->flush();
    }
}
