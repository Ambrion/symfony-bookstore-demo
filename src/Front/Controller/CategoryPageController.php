<?php

declare(strict_types=1);

namespace App\Front\Controller;

use App\Category\Entity\Category;
use App\Front\Filter\BookFilter;
use App\Front\Service\BookServiceInterface;
use App\Front\Service\CategoryServiceInterface;
use App\Front\Service\ImageServiceInterface;
use App\Settings\Service\SettingManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryPageController extends AbstractController
{
    public function __construct(
        private readonly CategoryServiceInterface $categoryService,
        private readonly BookServiceInterface $bookService,
        private readonly SettingManagerInterface $settingManager,
        private readonly ParameterBagInterface $parameterBag,
        private readonly ImageServiceInterface $imageService,
    ) {
    }

    #[Route('/category/{slug}', name: 'category_view', methods: ['GET'])]
    public function index(string $slug, Request $request): Response
    {
        $category = $this->categoryService->getCurrentCategoryBy(['slug' => $slug]);
        $categoryWithDescendants = $this->categoryService->getCategoryWithDescendants($category);

        $page = $request->query->getInt('page', 1);

        $searchTitle = $request->query->getString('title', '');
        $searchAuthor = $request->query->getString('author', '');
        $searchStatus = $request->query->getString('status', '');

        $filter = new BookFilter(
            title: $searchTitle,
            author: $searchAuthor,
            status: $searchStatus
        );

        $settingsLimit = null;
        $defaultLimit = $this->parameterBag->get('app.item_per_page_book_list');

        $settingPagination = $this->settingManager->findOneByName('app.item_per_page_book_list');
        if (!empty($settingPagination)) {
            $settingsLimit = $settingPagination->value;
        }

        $limit = (int) ($settingsLimit ?: $defaultLimit);

        $offset = ($page - 1) * $limit;

        $books = $this->bookService->findBooksByCategoryAndSubCategoryPaginated($categoryWithDescendants, $limit, $offset, $filter);
        $totalBooks = $this->bookService->countBooksByCategoryAndSubCategory($categoryWithDescendants, $filter);

        $totalPages = ceil($totalBooks / $limit);

        $imageDestination = $this->imageService->getImageUploadPathWithDomain();

        return $this->render('front/category/category_view.html.twig', [
            'category' => $category,
            'books' => $books,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalBooks' => $totalBooks,
            'breadcrumbs' => $this->buildBreadcrumbs($category),
            'imageDestination' => $imageDestination,
            'searchTitle' => $searchTitle,
            'searchAuthor' => $searchAuthor,
            'searchStatus' => $searchStatus,
        ]);
    }

    /**
     * Build breadcrumbs for a category.
     *
     * @return array<array{title: string, url: string|null}>
     */
    private function buildBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [
            ['title' => 'Главная', 'url' => $this->generateUrl('app_home')],
        ];

        $parents = [];
        $current = $category->getParentId();

        while ($current) {
            $parents[] = $current;
            $current = $current->getParentId();
        }

        $parents = array_reverse($parents);

        foreach ($parents as $parent) {
            $breadcrumbs[] = [
                'title' => $parent->getTitle(),
                'url' => $this->generateUrl('category_view', ['slug' => $parent->getSlug()]),
            ];
        }

        $breadcrumbs[] = [
            'title' => $category->getTitle(),
            'url' => null,
        ];

        return $breadcrumbs;
    }
}
