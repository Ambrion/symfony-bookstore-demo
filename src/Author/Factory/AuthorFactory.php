<?php

declare(strict_types=1);

namespace App\Author\Factory;

use App\Author\Entity\Author;

class AuthorFactory implements AuthorFactoryInterface
{
    public function create(string $title): Author
    {
        $author = new Author();

        $author->setTitle($title);

        return $author;
    }
}
