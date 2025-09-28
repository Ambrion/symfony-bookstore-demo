<?php

declare(strict_types=1);

namespace App\Front\Controller;

use App\Front\Service\BookServiceInterface;
use App\Front\Service\ImageServiceInterface;
use App\Settings\Service\SettingManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookPageController extends AbstractController
{
    public function __construct(
        private readonly BookServiceInterface $bookService,
        private readonly ImageServiceInterface $imageService,
        private readonly SettingManagerInterface $settingManager,
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    #[Route('/book/{slug}', name: 'book_view', methods: ['GET'])]
    public function index(string $slug): Response
    {
        $book = $this->bookService->findOneBookBy(['slug' => $slug]);

        $settingsLimit = null;
        $defaultLimit = $this->parameterBag->get('app.item_related_book_list');
        $setting = $this->settingManager->findOneByName('app.item_related_book_list');
        if (!empty($setting)) {
            $settingsLimit = $setting->value;
        }
        $limit = (int) ($settingsLimit ?: $defaultLimit);

        $relatedBooks = $this->bookService->findByCategoriesWithLimit($book->getCategories()->toArray(), $limit, [$book->getId()]);

        $imageDestination = $this->imageService->getImageUploadPathWithDomain();

        return $this->render('front/book/book_view.html.twig', [
            'book' => $book,
            'relatedBooks' => $relatedBooks,
            'imageDestination' => $imageDestination,
        ]);
    }
}
