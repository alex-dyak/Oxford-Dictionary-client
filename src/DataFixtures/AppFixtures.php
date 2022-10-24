<?php

namespace App\DataFixtures;

use App\Entity\Searches;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 1; $i <= 20; $i++) {
            $search = new Searches();
            $search->setWord('word_ '.$i);
            $search->setCnt(mt_rand(3, 100));
            $manager->persist($search);
        }



        $manager->flush();
    }
}
