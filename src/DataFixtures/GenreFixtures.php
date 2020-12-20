<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends BaseFixture
{
    private static $names = [
        'Genres',
        'Comics',
        'Science',
        'Architecture',
        'Economics'
    ];

    public function load(ObjectManager $manager)
    {
        $parentGenre = new Genre();
        $parentGenre->setName(self::$names[0]);

        $manager->persist($parentGenre);

        foreach (self::$names as $key => $name) {
            if ($key > 0) {
                $genre = new Genre();
                $genre->setName($name);
                $genre->setParent($parentGenre);

                $manager->persist($genre);
            }
        }

        $manager->flush();
    }
}
