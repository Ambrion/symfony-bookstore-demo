<?php

declare(strict_types=1);

namespace App\Book\DTO;

class BookDTO
{
    public function __construct(
        public ?string $title = null,

        public ?string $isbn = null,

        public ?int $pageCount = null,

        public ?string $publishedDate = null,

        public ?string $image = null,

        public ?string $shortDescription = null,

        public ?string $longDescription = null,

        public ?string $status = null,
        /**
         * @var array<string>|null
         */
        public ?array $authors = null,
        /**
         * @var array<string>|null
         */
        public ?array $categories = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): BookDTO
    {
        return new BookDTO(
            title: $data['title'] ?? null,
            isbn: $data['isbn'] ?? null,
            pageCount: $data['pageCount'] ?? 0,
            publishedDate: $data['publishedDate']['$date'] ?? null,
            image: $data['thumbnailUrl'] ?? null,
            shortDescription: $data['shortDescription'] ?? null,
            longDescription: $data['longDescription'] ?? null,
            status: $data['status'] ?? 'DRAFT',
            authors: $data['authors'] ?? [],
            categories: $data['categories'] ?? ['Новинки'],
        );
    }
}
