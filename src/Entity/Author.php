<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{

    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $family_name = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    /**
     * @var Collection<int, Book>
     */
    /** Owning Side */
    #[ORM\ManyToMany(targetEntity: Book::class, inversedBy: "authors")]
    #[ORM\JoinTable(name: "author_books")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "book_id", referencedColumnName: "id")]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamilyName(): ?string
    {
        return $this->family_name;
    }

    public function setFamilyName(string $family_name): static
    {
        $this->family_name = $family_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        $this->books->removeElement($book);

        return $this;
    }
}
