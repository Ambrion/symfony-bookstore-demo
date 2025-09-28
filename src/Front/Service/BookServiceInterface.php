<?php

declare(strict_types=1);

namespace App\Front\Service;

use App\Book\Entity\Book;
use App\Category\Entity\Category;
use App\Front\Filter\BookFilter;

interface BookServiceInterface
{
    /**
     * @param Category[] $categoryWithDescendants
     *
     * @return Book[]
     */
    public function findBooksByCategoryAndSubCategoryPaginated(array $categoryWithDescendants, int $limit, int $offset, ?BookFilter $filter = null): array;

    /**
     * @param Category[] $categoryWithDescendants
     */
    public function countBooksByCategoryAndSubCategory(array $categoryWithDescendants, ?BookFilter $filter = null): int;

    /**
     * @param array<string, mixed> $data
     */
    public function findOneBookBy(array $data): ?Book;

    /**
     * @param Category[] $categories
     * @param array<int> $exceptBookIds
     *
     * @return Book[]|null
     */
    public function findByCategoriesWithLimit(array $categories, int $limit, array $exceptBookIds = []): ?array;
}
