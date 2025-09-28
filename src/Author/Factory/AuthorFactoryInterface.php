<?php

declare(strict_types=1);

namespace App\Author\Factory;

use App\Author\Entity\Author;

interface AuthorFactoryInterface
{
    public function create(string $title): Author;
}
