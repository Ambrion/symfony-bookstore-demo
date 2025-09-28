<?php

declare(strict_types=1);

namespace App\Front\Controller;

use App\Category\Repository\CategoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAllCategoriesBy(['parentId' => null]);

        return $this->render('front/home/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
