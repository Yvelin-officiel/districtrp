<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {

        $categories = [];

        for ($i = 0; $i <= 10; $i++){
            $category = new Category;
            $category->setName('Category ' . $i);

            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i <= 50; $i++){
            $vehicle = new Vehicle;
            $vehicle->setName('Vehicle ' .  $i)
                -> setPrice(mt_rand(100, 1000))
                -> setImage('flamingo.png');

               $numCategories = mt_rand(1, 3);
               for ($j = 0; $j < $numCategories; $j++) {
                $category = $categories[mt_rand(0, 10)];
                $vehicle->addCategory($category);
            }
            
            $manager->persist($vehicle);
        }




        $user = new User();
        
        $user->setUsername('admin')
        ->setRoles(['ROLE_ADMIN'])
        ->setPlainpassword('password');

        $manager->persist($user);

        $manager->flush();
    }
}
