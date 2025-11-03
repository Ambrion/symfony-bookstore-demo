<?php

namespace App\Book\Entity;

use App\Author\Entity\Author;
use App\Book\Repository\BookRepository;
use App\Category\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Название обязательно к заполнению!')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[Assert\NotBlank(message: 'ISBN обязателен к заполнению!')]
    #[ORM\Column(length: 20)]
    private ?string $isbn = null;

    #[ORM\Column]
    private ?int $pageCount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $publishedDate = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $publishedTimeZone = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $longDescription = null;

    #[ORM\Column(length: 24)]
    private string $status;

    /**
     * @var ArrayCollection<int, Author>|PersistentCollection<int, Author>
     */
    #[ORM\JoinTable(name: 'author_to_book')]
    #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'author_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'App\Author\Entity\Author', cascade: ['persist'])]
    private ArrayCollection|PersistentCollection $authors;

    /**
     * @var ArrayCollection<int, Category>|PersistentCollection<int, Category>
     */
    #[ORM\JoinTable(name: 'category_to_book')]
    #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'category_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'App\Category\Entity\Category', cascade: ['persist'])]
    private ArrayCollection|PersistentCollection $categories;

    public function __construct()
    {
        $this->status = 'DRAFT';
        $this->authors = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount(int $pageCount): static
    {
        $this->pageCount = $pageCount;

        return $this;
    }

    public function getPublishedDate(): ?\DateTime
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(?\DateTime $publishedDate): static
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getPublishedTimeZone(): ?string
    {
        return $this->publishedTimeZone;
    }

    public function setPublishedTimeZone(?string $publishedTimeZone): static
    {
        $this->publishedTimeZone = $publishedTimeZone;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): static
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return ArrayCollection<int, Author>|PersistentCollection<int, Author>
     */
    public function getAuthors(): ArrayCollection|PersistentCollection
    {
        return $this->authors;
    }

    /**
     * @param ArrayCollection<int, Author> $authors
     */
    public function setAuthors(?ArrayCollection $authors): static
    {
        $this->authors = $authors;

        return $this;
    }

    /**
     * @return ArrayCollection<int, Category>|PersistentCollection<int, Category>
     */
    public function getCategories(): ArrayCollection|PersistentCollection
    {
        return $this->categories;
    }

    /**
     * @param ArrayCollection<int, Category> $categories
     */
    public function setCategories(ArrayCollection $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
