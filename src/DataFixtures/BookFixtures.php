<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Publisher;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $genres = $this->em->getRepository(Genre::class)->findAll();
        $authors = $this->em->getRepository(Author::class)->findAll();
        $publishers = $this->em->getRepository(Publisher::class)->findAll();

        for ($i = 0; $i < 50; $i++) {
            $book = new Book();
            $book->setTitle($this->faker->sentence);
            $book->setTotalPages($this->faker->numberBetween(100, 1000));
            $book->setRating($this->faker->randomFloat(2, 0, 5));
            $book->setIsbn($this->faker->ean13);
            $book->setPublishedAt($this->faker->dateTime());
            $book->setImg($this->faker->imageUrl(640, 480, 'books'));
            if ($i < 5) {
                $book->setPublisher($publishers[0]);
            }

            if ($i > 5 && $i < 20) {
                $book->setPublisher($publishers[$i]);
            }

            foreach ($authors as $k => $author) {
                if ($k >= $this->faker->numberBetween(3, 20)) {
                    break;
                }
                $book->addAuthor($author);
            }

            foreach ($genres as $k => $genre) {
                if ($k >= $this->faker->numberBetween(2, 5)) {
                    break;
                }
                $book->addGenre($genre);
            }


            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AuthorFixtures::class,
            GenreFixtures::class,
            PublisherFixtures::class
        ];
    }
}
