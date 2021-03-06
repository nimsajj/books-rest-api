<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @UniqueEntity("isbn")
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     */
    private $total_pages;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 10,
     *      notInRangeMessage = "You must be between {{ min }} and {{ max }} tall to enter",
     * )
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=13, unique=true)
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private $isbn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotNull
     */
    private $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Publisher::class, inversedBy="books")
     */
    private $publisher;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="books")
     */
    private $authors;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="books")
     */
    private $genres;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $img;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTotalPages(): ?int
    {
        return $this->total_pages;
    }

    public function setTotalPages(?int $total_pages): self
    {
        $this->total_pages = $total_pages;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function clearAuthors()
    {
        $this->authors = new ArrayCollection();
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    public function clearGenres()
    {
        $this->genres = new ArrayCollection();
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }
}
