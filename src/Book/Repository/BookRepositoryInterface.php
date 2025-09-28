<?php

declare(strict_types=1);

namespace App\Book\Repository;

use App\Book\Entity\Book;
use App\Category\Entity\Category;
use App\Front\Filter\BookFilter;

interface BookRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function findOneBookBy(array $data): ?Book;

    /**
     * @param Category[] $categories
     *
     * @return Book[]
     */
    public function findByCategoriesPaginated(array $categories, int $limit, int $offset, ?BookFilter $filter = null): array;

    /**
     * @param Category[] $categories
     * @param array<int> $exceptBookIds
     *
     * @return Book[]
     */
    public function findByCategoriesWithLimit(array $categories, int $limit, array $exceptBookIds = []): array;

    /**
     * @param Category[] $categories
     */
    public function countByCategories(array $categories, ?BookFilter $filter = null): int;
}
