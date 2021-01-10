<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GenreController extends AbstractController
{
    private $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    /**
     * @Route("/api/genres", name="addGenre", methods={"POST"})
     */
    public function addGenre(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $genre = $this->genreRepository->build($data);

        $errors = $validator->validate($genre);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $genreCreated = $this->genreRepository->save($genre);

        return $this->json($genreCreated, Response::HTTP_CREATED, [], ['ignored_attributes' => ['books', 'children']]);
    }

    /**
     * @Route("/api/genres/{id}", name="updateGenre", methods={"PUT"})
     */
    public function updateGenre(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $currentGenre = $this->genreRepository->findOneBy(['id' => $id]);

        $genre = $this->genreRepository->buildIfInformed($data, $currentGenre);

        $errors = $validator->validate($genre);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $genreUpdated = $this->genreRepository->save($genre);

        return $this->json($genreUpdated, Response::HTTP_OK, [], ['ignored_attributes' => ['books', 'children']]);
    }

    /**
     * @Route("/api/genres/{id}", name="deleteGenre", methods={"DELETE"})
     */
    public function deleteGenre(int $id): Response
    {
        $genre = $this->genreRepository->findOneBy(['id' => $id]);

        if (empty($genre)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        $this->genreRepository->remove($genre);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/genres", name="getAllGenre", methods={"GET"})
     */
    public function getAllGenre(): Response
    {
        $genres = $this->genreRepository->findAll();

        if (empty($genres)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($genres, Response::HTTP_OK, [], ['ignored_attributes' => ['books', 'children']]);
    }

    /**
     * @Route("/api/genres/{id}", name="getGenre", methods={"GET"})
     */
    public function getGenre(int $id): Response
    {
        $genre = $this->genreRepository->findOneBy(['id' => $id]);

        if (empty($genre)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($genre, Response::HTTP_OK, [], ['ignored_attributes' => ['children']]);
    }

    /**
     * @Route("/api/genres/{id}/books", name="getBooksByGenre", methods={"GET"})
     */
    public function getBooksByGenre(int $id): Response
    {
        $booksByGenre = $this->genreRepository->findOneBy(['id' => $id]);

        if (empty($booksByGenre)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($booksByGenre, Response::HTTP_OK, [], ['ignored_attributes' => ['authors', 'genres', 'children', 'publisher']]);
    }
}
