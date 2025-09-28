<?php

declare(strict_types=1);

namespace App\Parser\Service\Book;

interface ParseBookFromUrlInJsonServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function urlHandle(string $url, string $importFilePath, ?int $getBooks = null): array;
}
