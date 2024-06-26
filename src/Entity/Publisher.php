<?php

namespace App\Entity;

use App\Repository\PublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: PublisherRepository::class)]
class Publisher
{

    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $publisher_name = null;

    #[ORM\Column(length: 255)]
    private ?string $publisher_address = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'publisher')]
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

    public function getPublisherName(): ?string
    {
        return $this->publisher_name;
    }

    public function setPublisherName(string $publisher_name): static
    {
        $this->publisher_name = $publisher_name;

        return $this;
    }

    public function getPublisherAddress(): ?string
    {
        return $this->publisher_address;
    }

    public function setPublisherAddress(string $publisher_address): static
    {
        $this->publisher_address = $publisher_address;

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
            $book->setPublisher($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getPublisher() === $this) {
                $book->setPublisher(null);
            }
        }

        return $this;
    }
}
