<?php

declare(strict_types=1);

namespace App\Category\Repository;

use App\Category\Entity\Category;

interface CategoryRepositoryInterface
{
    public function isExistCategoryByTitle(string $title): int;

    /**
     * @param array<string> $titles
     *
     * @return array<string>
     */
    public function findExistingCategoryTitle(array $titles): array;

    /**
     * @param array<string> $titles
     *
     * @return array<Category>|null
     */
    public function findExistingCategoryByTitle(array $titles): ?array;

    /**
     * @param array<string, mixed> $data
     */
    public function getCurrentCategoryBy(array $data): Category;

    /**
     * @param array<string, mixed> $data
     *
     * @return Category[]|null
     */
    public function findAllCategoriesBy(array $data): ?array;
}
