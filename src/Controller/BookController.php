<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\PublisherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BookController extends AbstractController
{
    private $bookRepository;
    private $publisherRepository;
    private $authorRepository;
    private $genreRepository;

    public function __construct(BookRepository $bookRepository, PublisherRepository $publisherRepository, AuthorRepository $authorRepository, GenreRepository $genreRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->publisherRepository = $publisherRepository;
        $this->authorRepository = $authorRepository;
        $this->genreRepository = $genreRepository;
    }

    /**
     * @Route("/api/books", name="addBook", methods={"POST"})
     */
    public function addBook(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['title']) || empty($data['totalPages']) || empty($data['isbn']) || empty($data['publishedAt']) || empty($data['publisherId']) || empty($data['authorIds']) || empty($data['genreIds'])) {
            return $this->json(['error' => 'Expecting mandatory parameters!'], Response::HTTP_ACCEPTED);
        }

        $publisher = $this->publisherRepository->findOneBy(['id' => $data['publisherId']]);

        if (empty($publisher)) {
            return $this->json(['error' => 'Publisher is required'], Response::HTTP_ACCEPTED);
        }

        $authors = $this->authorRepository->findBy(['id' => $data['authorIds']]);

        if (empty($authors)) {
            return $this->json(['error' => 'One or many author is required'], Response::HTTP_ACCEPTED);
        }

        $genres = $this->genreRepository->findBy(['id' => $data['genreIds']]);

        if (empty($genres)) {
            return $this->json(['error' => 'One or many genre is required'], Response::HTTP_ACCEPTED);
        }

        $book = new Book();
        $book->setTitle($data['title'])
            ->setTotalPages($data['totalPages'])
            ->setIsbn($data['isbn'])
            ->setPublishedAt(new \DateTime($data['publishedAt']))
            ->setPublisher($publisher);

        if (!empty($data['rating'])) {
            $book->setRating($data['rating']);
        }

        foreach ($authors as $author) {
            $book->addAuthor($author);
        }

        foreach ($genres as $genre) {
            $book->addGenre($genre);
        }

        $bookCreated = $this->bookRepository->saveBook($book);

        return $this->json($bookCreated, Response::HTTP_CREATED, [], ['ignored_attributes' => ['books', 'parent', 'children']]);
    }

    /**
     * @Route("/api/books/{id}", name="updateBook", methods={"PUT"})
     */
    public function updateBook(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $book = $this->bookRepository->findOneBy(['id' => $id]);
        if (empty($book)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        empty($data['isbn']) ? true : $book->setIsbn($data['isbn']);
        empty($data['title']) ? true : $book->setTitle($data['title']);
        empty($data['totalPages']) ? true : $book->setTotalPages($data['totalPages']);
        empty($data['publishedAt']) ? true : $book->setPublishedAt(new \DateTime($data['publishedAt']));
        empty($data['rating']) ? true : $book->setRating($data['rating']);

        if (!empty($data['publisherId']) && $data['publisherId'] !== $book->getPublisher()->getId()) {
            $publisher = $this->publisherRepository->findOneBy(['id' => $data['publisherId']]);

            if (empty($publisher)) {
                return $this->json(['error' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
            }

            $book->setPublisher($publisher);
        }

        if (!empty($data['authorIds'])) {
            $authors = $this->authorRepository->findBy(['id' => $data['authorIds']]);

            if (empty($authors)) {
                return $this->json(['error' => 'Author(s) not found'], Response::HTTP_NOT_FOUND);
            }

            $book->clearAuthors();
            foreach ($authors as $author) {
                $book->addAuthor($author);
            }
        }

        if (!empty($data['genreIds'])) {
            $genres = $this->genreRepository->findBy(['id' => $data['genreIds']]);

            if (empty($genres)) {
                return $this->json(['error' => 'Genre(s) not found'], Response::HTTP_NOT_FOUND);
            }

            $book->clearGenres();
            foreach ($genres as $genre) {
                $book->addGenre($genre);
            }
        }

        $bookUpdated = $this->bookRepository->saveBook($book);

        return $this->json($bookUpdated, Response::HTTP_CREATED, [], ['ignored_attributes' => ['books', 'parent', 'children']]);
    }

    /**
     * @Route("/api/books/{id}", name="deleteBook", methods={"DELETE"})
     */
    public function deleteBook(int $id): Response
    {
        $book = $this->bookRepository->findOneBy(['id' => $id]);

        if (empty($book)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        $this->bookRepository->removeBook($book);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/books", name="getAllBooks", methods={"GET"})
     */
    public function getAllBooks(): Response
    {
        $books = $this->bookRepository->findAll();

        return $this->json($books, Response::HTTP_OK, [], ['ignored_attributes' => ['authors', 'genres', 'publisher']]);
    }

    /**
     * @Route("/api/books/{id}", name="getBook", methods={"GET"})
     */
    public function getBook(int $id): Response
    {
        $book = $this->bookRepository->findOneBy(['id' => $id]);

        if (empty($book)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($book, Response::HTTP_OK, [], ['ignored_attributes' => ['authors', 'genres', 'books']]);
    }

    /**
     * @Route("/api/books/{id}/authors", name="getAuthorsByBook", methods={"GET"})
     */
    public function getAuthorsByBook(int $id): Response
    {
        $book = $this->bookRepository->findOneBy(['id' => $id]);

        if (empty($book)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($book, Response::HTTP_OK, [], ['ignored_attributes' => ['genres', 'publisher', 'books']]);
    }

    /**
     * @Route("/api/books/{id}/genres", name="getAuthorsByBook", methods={"GET"})
     */
    public function getGenresByBook(int $id): Response
    {
        $book = $this->bookRepository->findOneBy(['id' => $id]);

        if (empty($book)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($book, Response::HTTP_OK, [], ['ignored_attributes' => ['authors', 'children', 'parent', 'publisher', 'books']]);
    }
}
