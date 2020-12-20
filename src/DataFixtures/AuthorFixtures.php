<?php

namespace App\DataFixtures;

use App\Entity\Author;

use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends BaseFixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $author = new Author();
            $author->setFirstName($this->faker->firstName);
            $author->setLastName($this->faker->lastName);

            $manager->persist($author);
        }

        $manager->flush();
    }
}
