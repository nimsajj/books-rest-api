<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * @Route("/api/books", name="addBook", methods={"POST"})
     */
    public function addBook(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['title']) || empty($data['totalPages']) || empty($data['isbn']) || empty($data['publishedAt']) || empty($data['publisherId']) || empty($data['authorIds']) || empty($data['genreIds'])) {
            return $this->json(['error' => 'Expecting mandatory parameters!'], Response::HTTP_PAYMENT_REQUIRED);
        }

        $book = $this->bookRepository->build($data);

        if (empty($book->getPublisher())) {
            return $this->json(['error' => 'Publisher is required'], Response::HTTP_NOT_FOUND);
        }

        if ($book->getAuthors()->isEmpty()) {
            return $this->json(['error' => 'One or many author is required'], Response::HTTP_NOT_FOUND);
        }

        if ($book->getGenres()->isEmpty()) {
            return $this->json(['error' => 'One or many genre is required'], Response::HTTP_NOT_FOUND);
        }

        $errors = $validator->validate($book);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $bookCreated = $this->bookRepository->save($book);

        return $this->json($bookCreated, Response::HTTP_CREATED, [], ['ignored_attributes' => ['books', 'parent', 'children']]);
    }

    /**
     * @Route("/api/books/{id}", name="updateBook", methods={"PUT"})
     */
    public function updateBook(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $currentBook = $this->bookRepository->findOneBy(['id' => $id]);
        if (empty($currentBook)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $book = $this->bookRepository->buildIfInformed($data, $currentBook);

        if (empty($book->getPublisher())) {
            return $this->json(['error' => 'Publisher not found'], Response::HTTP_NOT_FOUND);
        }

        if ($book->getAuthors()->isEmpty()) {
            return $this->json(['error' => 'Author(s) not found'], Response::HTTP_NOT_FOUND);
        }

        if ($book->getGenres()->isEmpty()) {
            return $this->json(['error' => 'Genre(s) not found'], Response::HTTP_NOT_FOUND);
        }

        $errors = $validator->validate($book);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $bookUpdated = $this->bookRepository->save($book);

        return $this->json($bookUpdated, Response::HTTP_OK, [], ['ignored_attributes' => ['books', 'parent', 'children']]);
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

        $this->bookRepository->remove($book);

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
     * @Route("/api/books/{id}/genres", name="getGenresByBook", methods={"GET"})
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
