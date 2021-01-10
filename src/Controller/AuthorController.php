<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends AbstractController
{
    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route("/api/authors", name="addAuthor", methods={"POST"})
     */
    public function addAuthor(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $author = $this->authorRepository->build($data);

        $errors = $validator->validate($author);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $authorCreated = $this->authorRepository->save($author);

        return $this->json($authorCreated, Response::HTTP_CREATED,);
    }

    /**
     * @Route("/api/authors/{id}", name="updateAuthor", methods={"PUT"})
     */
    public function updateAuthor(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $currentAuthor = $this->authorRepository->findOneBy(['id' => $id]);

        $author = $this->authorRepository->buildIfInformed($data, $currentAuthor);

        $errors = $validator->validate($author);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updatedAuthor = $this->authorRepository->save($author);

        return $this->json($updatedAuthor, Response::HTTP_OK);
    }

    /**
     * @Route("/api/authors/{id}", name="deleteAuthor", methods={"DELETE"})
     */
    public function deleteAuthor(int $id): Response
    {
        $author = $this->authorRepository->findOneBy(['id' => $id]);

        if (empty($author)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        $this->authorRepository->remove($author);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/authors", name="getAllAuthor", methods={"GET"})
     */
    public function getAllAuthor(): Response
    {
        $authors = $this->authorRepository->findAll();

        if (empty($authors)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($authors, Response::HTTP_OK, [], ['ignored_attributes' => ['books']]);
    }

    /**
     * @Route("/api/authors/{id}", name="getAuthor", methods={"GET"})
     */
    public function getAuthor(int $id): Response
    {
        $author = $this->authorRepository->findOneBy(['id' => $id]);

        if (empty($author)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($author, Response::HTTP_OK, [], ['ignored_attributes' => ['books']]);
    }

    /**
     * @Route("/api/authors/{id}/books", name="getBooksByAuthor", methods={"GET"})
     */
    public function getBooksByAuthor(int $id): Response
    {
        $booksByAuthor = $this->authorRepository->findOneBy(['id' => $id]);

        if (empty($booksByAuthor)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($booksByAuthor, Response::HTTP_OK, [], ['ignored_attributes' => ['authors', 'genres', 'publisher']]);
    }
}
