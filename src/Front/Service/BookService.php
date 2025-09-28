<?php

declare(strict_types=1);

namespace App\Front\Service;

use App\Book\Entity\Book;
use App\Book\Repository\BookRepositoryInterface;
use App\Category\Entity\Category;
use App\Front\Filter\BookFilter;

readonly class BookService implements BookServiceInterface
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
    ) {
    }

    /**
     * @param Category[] $categoryWithDescendants
     *
     * @return Book[]
     */
    public function findBooksByCategoryAndSubCategoryPaginated(array $categoryWithDescendants, int $limit, int $offset, ?BookFilter $filter = null): array
    {
        return $this->bookRepository->findByCategoriesPaginated($categoryWithDescendants, $limit, $offset, $filter);
    }

    /**
     * @param Category[] $categoryWithDescendants
     */
    public function countBooksByCategoryAndSubCategory(array $categoryWithDescendants, ?BookFilter $filter = null): int
    {
        return $this->bookRepository->countByCategories($categoryWithDescendants, $filter);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function findOneBookBy(array $data): ?Book
    {
        return $this->bookRepository->findOneBookBy($data);
    }

    /**
     * @param Category[] $categories
     * @param array<int> $exceptBookIds
     *
     * @return Book[]|null
     */
    public function findByCategoriesWithLimit(array $categories, int $limit, array $exceptBookIds = []): ?array
    {
        return $this->bookRepository->findByCategoriesWithLimit($categories, $limit, $exceptBookIds);
    }
}
