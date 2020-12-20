<?php

namespace App\DataFixtures;

use App\Entity\Publisher;
use Doctrine\Persistence\ObjectManager;

class PublisherFixtures extends BaseFixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $publisher = new Publisher();
            $publisher->setName($this->faker->name);

            $manager->persist($publisher);
        }

        $manager->flush();
    }
}
