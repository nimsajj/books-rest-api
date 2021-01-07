<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function addGenre(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['error' => 'Expecting mandatory parameters!'], Response::HTTP_ACCEPTED);
        }

        $genre = new Genre();
        $genre
            ->setName($data['name']);

        if (!empty($data['parent'])) {
            $parent = $this->genreRepository->findOneBy(['name' => $data['parent']]);

            if ($parent) {
                $genre->setParent($parent);
            }
        }

        $genreCreated = $this->genreRepository->saveGenre($genre);

        return $this->json($genreCreated, Response::HTTP_CREATED, [], ['ignored_attributes' => ['books', 'children']]);
    }

    /**
     * @Route("/api/genres/{id}", name="updateGenre", methods={"PUT"})
     */
    public function updateGenre(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $genre = $this->genreRepository->findOneBy(['id' => $id]);

        empty($data['name']) ? true : $genre->setName($data['name']);

        if (!empty($data['parent'])) {
            $parent = $this->genreRepository->findOneBy(['name' => $data['parent']]);

            if ($parent) {
                $genre->setParent($parent);
            }
        }

        $genreUpdated = $this->genreRepository->saveGenre($genre);

        return $this->json($genreUpdated, Response::HTTP_OK, [], ['ignored_attributes' => ['books', 'children', 'parent']]);
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

        $this->genreRepository->removeGenre($genre);

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
