<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'books')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idBook')]
    private ?int $idBook = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(name: 'idGenre', referencedColumnName: 'idGenre', nullable: false)]
    private ?Genre $genre = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(name: 'idAuthor', referencedColumnName: 'idAuthor', nullable: false)]
    private ?Author $author = null;
    
    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $isbn = null;

    #[ORM\OneToOne(mappedBy: 'book', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'idBorrow', referencedColumnName: 'idBorrow', nullable: false)]
    private ?Borrow $borrow = null;

    #[ORM\Column(name: 'isBorrowed')]
    private ?bool $isBorrowed = null;

    #[ORM\Column(length: 30)]
    private ?string $cover = null;

    #[ORM\Column(name: 'publishedDate', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $publishedDate = null;

    #[ORM\Column(length: 255, name: 'originalLanguage')]
    private ?string $originalLanguage = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Evaluation::class)]
    private Collection $evaluations;
    
    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Evaluation::class)]
    private Collection $favorites;

    #[ORM\ManyToOne(targetEntity: Genre::class, inversedBy:"books", cascade:["persist"])]
    #[ORM\JoinColumn(name:'idGenre', referencedColumnName:'idGenre')]
    // La variable de la relation (Foreign Key)
    private $genre;
    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getIdBook(): ?int
    {
        return $this->idBook;
    }

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy:"books", cascade:["persist"])]
    #[ORM\JoinColumn(name:'idAuthor', referencedColumnName:'idAuthor')]
    // La variable de la relation (Foreign Key)
    private $author;

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(Genre $genre): self
    public function setGenre(?Genre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }


    public function getIdBook(): ?int
    {
        return $this->idBook;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getBorrow(): ?Borrow
    {
        return $this->borrow;
    }

    public function setBorrow(Borrow $borrow): static
    {
        // set the owning side of the relation if necessary
        if ($borrow->getBook() !== $this) {
            $borrow->setBook($this);
        }

        $this->borrow = $borrow;

        return $this;
    }

    public function isIsBorrowed(): ?bool
    {
        return $this->isBorrowed;
    }

    public function setIsBorrowed(bool $isBorrowed): static
    {
        $this->isBorrowed = $isBorrowed;

        return $this;
    }

    public function getCover(): ?int
    {
        return $this->cover;
    }

    public function setCover(int $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getPublishedDate(): ?\DateTimeInterface
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(\DateTimeInterface $publishedDate): static
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getOriginalLanguage(): ?string
    {
        return $this->originalLanguage;
    }

    public function setOriginalLanguage(string $originalLanguage): static
    {
        $this->originalLanguage = $originalLanguage;

        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): static
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setBook($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): static
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getBook() === $this) {
                $evaluation->setBook(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setBook($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getBook() === $this) {
                $favorite->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setBook($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getBook() === $this) {
                $reservation->setBook(null);
            }
        }

        return $this;
    }
}