<?php

declare(strict_types=1);

namespace App\Front\Filter;

class BookFilter
{
    public function __construct(
        private ?string $title = null,
        private ?string $author = null,
        private ?string $status = null,
    ) {
        if (!empty($this->title)) {
            $this->setTitle(trim($this->title));
        }

        if (!empty($this->author)) {
            $this->setAuthor(trim($this->author));
        }

        if (!empty($this->status)) {
            $this->setStatus(trim($this->status));
        }
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
