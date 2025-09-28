<?php

declare(strict_types=1);

namespace App\Author\Repository;

use App\Author\Entity\Author;

interface AuthorRepositoryInterface
{
    /**
     * @param array<string> $titles
     *
     * @return array<string>
     */
    public function findExistingAuthorTitle(array $titles): array;

    /**
     * @param array<string> $titles
     *
     * @return array<Author>
     */
    public function findExistingAuthorByTitle(array $titles): array;
}
