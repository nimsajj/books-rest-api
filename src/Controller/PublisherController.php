<?php

namespace App\Controller;

use App\Entity\Publisher;
use App\Repository\PublisherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PublisherController extends AbstractController
{
    private $publisherRepository;

    public function __construct(PublisherRepository $publisherRepository)
    {
        $this->publisherRepository = $publisherRepository;
    }

    /**
     * @Route("/api/publishers", name="addPublisher", methods={"POST"})
     */
    public function addPublisher(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $publisher = $this->publisherRepository->build($data);

        $errors = $validator->validate($publisher);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $publisherCreated = $this->publisherRepository->save($publisher);

        return $this->json($publisherCreated, Response::HTTP_CREATED, [], ['ignored_attributes' => ['books']]);
    }

    /**
     * @Route("/api/publishers/{id}", name="updatePublisher", methods={"PUT"})
     */
    public function updatePublisher(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $currentPublisher = $this->publisherRepository->findOneBy(['id' => $id]);

        if (empty($currentPublisher)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $publisher = $this->publisherRepository->buildIfInformed($data, $currentPublisher);

        $errors = $validator->validate($publisher);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updatedPublisher = $this->publisherRepository->save($publisher);

        return $this->json($updatedPublisher, Response::HTTP_OK, [], ['ignored_attributes' => ['books']]);
    }

    /**
     * @Route("/api/publishers/{id}", name="deletePublisher", methods={"DELETE"})
     */
    public function deletePublisher(int $id): Response
    {
        $publisher = $this->publisherRepository->findOneBy(['id' => $id]);

        if (empty($publisher)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        $this->publisherRepository->remove($publisher);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/publishers", name="getAllPublisher", methods={"GET"})
     */
    public function getAllPublisher(): Response
    {
        $publishers = $this->publisherRepository->findAll();

        if (empty($publishers)) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($publishers, Response::HTTP_OK, [], ['ignored_attributes' => ['books']]);
    }

    /**
     * @Route("/api/publishers/{id}", name="getPublisher", methods={"GET"})
     */
    public function getPublisher(int $id): Response
    {
        $publisher = $this->publisherRepository->findOneBy(['id' => $id]);

        if (empty($publisher)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($publisher, Response::HTTP_OK, [], ['ignored_attributes' => ['books']]);
    }

    /**
     * @Route("/api/publishers/{id}/books", name="getBooksByGenre", methods={"GET"})
     */
    public function getBooksByPublisher(int $id): Response
    {
        $booksByPublisher = $this->publisherRepository->findOneBy(['id' => $id]);

        if (empty($booksByPublisher)) {
            return $this->json(['error' => 'Not found', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($booksByPublisher, Response::HTTP_OK, [], ['ignored_attributes' => ['authors', 'genres', 'publisher']]);
    }
}
