<?php

declare(strict_types=1);

namespace App\Book\Factory;

use App\Book\DTO\BookDTO;
use App\Book\Entity\Book;

interface BookFactoryInterface
{
    public function create(BookDTO $bookDTO, ?Book $existingBook = null, bool $dryRun = false): Book;

    public function downloadImage(string $imageUrl, string $slug, bool $dryRun = false): ?string;
}
