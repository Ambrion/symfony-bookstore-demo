<?php

declare(strict_types=1);

namespace App\Front\Service;

use App\Category\Entity\Category;
use App\Category\Repository\CategoryRepositoryInterface;

readonly class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function getCurrentCategoryBy(array $data): Category
    {
        return $this->categoryRepository->getCurrentCategoryBy($data);
    }

    /**
     * Получаем все категории-потомки данной категории.
     *
     * @return Category[]
     */
    public function getCategoryWithDescendants(Category $category): array
    {
        $categories = [$category];
        $children = $category->getChildren()->toArray();

        while (!empty($children)) {
            $child = array_shift($children);
            $categories[] = $child;
            $grandChildren = $child->getChildren()->toArray();
            $children = array_merge($children, $grandChildren);
        }

        return $categories;
    }
}
