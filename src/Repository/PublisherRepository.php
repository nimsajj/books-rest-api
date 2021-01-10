<?php

namespace App\Repository;

use App\Entity\Publisher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publisher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publisher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publisher[]    findAll()
 * @method Publisher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublisherRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Publisher::class);
        $this->manager = $manager;
    }

    public function build(array $data): Publisher
    {
        $name = $data['name'] ?? '';

        $publisher = new Publisher();

        return $publisher
            ->setName($name);
    }

    public function buildIfInformed($data, Publisher $publisher): Publisher
    {
        empty($data['name']) ? true : $publisher->setName($data['name']);

        return $publisher;
    }

    public function save(Publisher $publisher): Publisher
    {
        $this->manager->persist($publisher);
        $this->manager->flush();

        return $publisher;
    }

    public function remove(Publisher $publisher): void
    {
        $this->manager->remove($publisher);
        $this->manager->flush();
    }
}
