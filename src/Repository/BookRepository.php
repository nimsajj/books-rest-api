<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    private $manager;
    private $publisherRepository;
    private $authorRepository;
    private $genreRepository;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager, PublisherRepository $publisherRepository, AuthorRepository $authorRepository, GenreRepository $genreRepository)
    {
        parent::__construct($registry, Book::class);
        $this->manager = $manager;
        $this->publisherRepository = $publisherRepository;
        $this->authorRepository = $authorRepository;
        $this->genreRepository = $genreRepository;
    }

    public function build(array $data): Book
    {
        $book = new Book();

        $publisher = $this->publisherRepository->findOneBy(['id' => $data['publisherId']]);
        $authors = $this->authorRepository->findBy(['id' => $data['authorIds']]);
        $genres = $this->genreRepository->findBy(['id' => $data['genreIds']]);

        $book->setTitle($data['title'])
            ->setTotalPages($data['totalPages'])
            ->setIsbn($data['isbn'])
            ->setPublisher($publisher);

        if (strtotime($data['publishedAt'])) {
            $book->setPublishedAt(new \DateTime($data['publishedAt']));
        }

        if (!empty($data['rating'])) {
            $book->setRating($data['rating']);
        }

        foreach ($authors as $author) {
            $book->addAuthor($author);
        }

        foreach ($genres as $genre) {
            $book->addGenre($genre);
        }

        return $book;
    }

    public function buildIfInformed($data, Book $book): Book
    {
        $publishedAt = strtotime($data['publishedAt']) ? $data['publishedAt'] : null;
        empty($data['isbn']) ? true : $book->setIsbn($data['isbn']);
        empty($data['title']) ? true : $book->setTitle($data['title']);
        empty($data['totalPages']) ? true : $book->setTotalPages($data['totalPages']);
        empty($data['publishedAt']) ? true : $book->setPublishedAt($publishedAt);
        empty($data['rating']) ? true : $book->setRating($data['rating']);

        if (!empty($data['publisherId']) && $data['publisherId'] !== $book->getPublisher()->getId()) {
            $publisher = $this->publisherRepository->findOneBy(['id' => $data['publisherId']]);

            $book->setPublisher($publisher);
        }

        if (!empty($data['authorIds'])) {
            $authors = $this->authorRepository->findBy(['id' => $data['authorIds']]);

            $book->clearAuthors();
            foreach ($authors as $author) {
                $book->addAuthor($author);
            }
        }

        if (!empty($data['genreIds'])) {
            $genres = $this->genreRepository->findBy(['id' => $data['genreIds']]);

            $book->clearGenres();
            foreach ($genres as $genre) {
                $book->addGenre($genre);
            }
        }

        return $book;
    }

    public function save(Book $book): Book
    {
        $this->manager->persist($book);
        $this->manager->flush();

        return $book;
    }

    public function remove(Book $book): void
    {
        $this->manager->remove($book);
        $this->manager->flush();
    }
}
