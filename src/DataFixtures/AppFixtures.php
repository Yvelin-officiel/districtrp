<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i <= 50; $i++){
            $vehicle = new Vehicle;
            $vehicle->setName('Vehicle ' .  $i)
                ->setPrice(mt_rand(100, 1000));

            $manager->persist($vehicle);
        }

        $manager->flush();
    }
}
