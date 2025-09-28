<?php

declare(strict_types=1);

namespace App\Front\Service;

use App\Category\Entity\Category;

interface CategoryServiceInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function getCurrentCategoryBy(array $data): Category;

    /**
     * @return Category[]
     */
    public function getCategoryWithDescendants(Category $category): array;
}
