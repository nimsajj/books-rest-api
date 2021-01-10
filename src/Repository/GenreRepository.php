<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Genre::class);
        $this->manager = $manager;
    }

    public function build(array $data, ?Genre $currentGenre = null): Genre
    {
        $genre = $currentGenre ? $currentGenre : new Genre();

        $name = $data['name'] ?? "";

        $genre
            ->setName($name);

        if (!empty($data['parent'])) {
            $parent = $this->findOneBy(['name' => $data['parent']]);

            if ($parent) {
                $genre->setParent($parent);
            }
        }

        return $genre;
    }

    public function buildIfInformed($data, Genre $genre): Genre
    {
        empty($data['name']) ? true : $genre->setName($data['name']);

        if (!empty($data['parent'])) {
            $parent = $this->findOneBy(['name' => $data['parent']]);

            if ($parent) {
                $genre->setParent($parent);
            }
        }

        return $genre;
    }

    public function save(Genre $genre): Genre
    {
        $this->manager->persist($genre);
        $this->manager->flush();

        return $genre;
    }

    public function remove(Genre $genre): void
    {
        $this->manager->remove($genre);
        $this->manager->flush();
    }
}
