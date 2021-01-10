<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Author::class);
        $this->manager = $manager;
    }

    public function build(array $data): Author
    {
        $author =  new Author();

        $firstName = $data['firstName'] ??  "";
        $lastName = $data['lastName'] ?? "";
        $middleName = $data['middleName'] ??  "";

        $author
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setMiddleName($middleName);

        return $author;
    }

    public function buildIfInformed($data, Author $author): Author
    {
        empty($data['firstName']) ? true : $author->setFirstName($data['firstName']);
        empty($data['lastName']) ? true : $author->setLastName($data['lastName']);
        empty($data['middleName']) ? true : $author->setMiddleName($data['middleName']);

        return $author;
    }

    public function save(Author $author): Author
    {
        $this->manager->persist($author);
        $this->manager->flush();

        return $author;
    }

    public function remove(Author $author): void
    {
        $this->manager->remove($author);
        $this->manager->flush();
    }
}
