<?php

declare(strict_types=1);

namespace App\Category\Factory;

use App\Category\Entity\Category;

interface CategoryFactoryInterface
{
    public function create(string $title, ?Category $parentId = null): Category;
}
