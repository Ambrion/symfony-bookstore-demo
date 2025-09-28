<?php

declare(strict_types=1);

namespace App\Book\Factory;

use App\Author\Repository\AuthorRepositoryInterface;
use App\Book\DTO\BookDTO;
use App\Book\Entity\Book;
use App\Category\Repository\CategoryRepositoryInterface;
use App\Parser\Service\Book\TimeZoneConverter;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookFactory implements BookFactoryInterface
{
    public function __construct(
        public LoggerInterface $logger,
        public SluggerInterface $slugger,
        public ParameterBagInterface $parameterBag,
        public AuthorRepositoryInterface $authorRepository,
        public CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    public function create(BookDTO $bookDTO, ?Book $existingBook = null, bool $dryRun = false): Book
    {
        if ($existingBook) {
            $book = $existingBook;
        } else {
            $book = new Book();
        }

        $book->setTitle($bookDTO->title);
        $book->setIsbn($bookDTO->isbn);
        $book->setPageCount($bookDTO->pageCount);
        $book->setShortDescription($bookDTO->shortDescription);
        $book->setLongDescription($bookDTO->longDescription);
        $book->setStatus($bookDTO->status);

        if (!empty($bookDTO->publishedDate)) {
            $publishedDate = new \DateTime($bookDTO->publishedDate);
            $book->setPublishedDate($publishedDate);

            $timezoneOffset = $publishedDate->getTimezone()->getName();
            $timezoneName = TimeZoneConverter::offsetToTimezoneName($timezoneOffset);
            $book->setPublishedTimeZone($timezoneName);
        }

        $slug = $this->slugger->slug($bookDTO->title)->lower()->toString();
        $book->setSlug($slug);

        if (isset($bookDTO->image)) {
            $imagePath = $this->downloadImage($bookDTO->image, $slug, $dryRun);
            if ($imagePath) {
                $book->setImage($imagePath);
            }
        }

        $authors = new ArrayCollection();
        if (!empty($bookDTO->authors)) {
            $existingAuthors = $this->authorRepository->findExistingAuthorByTitle($bookDTO->authors);

            foreach ($existingAuthors as $author) {
                $authors->add($author);
            }
        }

        $book->setAuthors($authors);

        $categories = new ArrayCollection();
        if (!empty($bookDTO->categories)) {
            $existingCategories = $this->categoryRepository->findExistingCategoryByTitle($bookDTO->categories);

            foreach ($existingCategories as $category) {
                $categories->add($category);
            }
        }

        $book->setCategories($categories);

        return $book;
    }

    public function downloadImage(string $imageUrl, string $slug, bool $dryRun = false): ?string
    {
        try {
            $filesystem = new Filesystem();
            $appPublicUploadDir = $this->parameterBag->get('app.public_upload_dir');
            $appImageUploadDir = $this->parameterBag->get('app.image_upload_dir');

            $uploadDir = $appPublicUploadDir.$appImageUploadDir;

            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $slug.'.'.$extension;

            if (!$dryRun) {
                $filesystem->mkdir($uploadDir);

                $fullPath = $uploadDir.'/'.$filename;

                $client = HttpClient::create();
                $response = $client->request('GET', $imageUrl);

                if (200 === $response->getStatusCode()) {
                    $imageContent = $response->getContent();
                    file_put_contents($fullPath, $imageContent);

                    return $filename;
                }
            } else {
                return $filename;
            }
        } catch (\Exception $e) {
            $this->logger->error('Ошибка загрузки изображения: '.$e->getMessage(), ['url' => $imageUrl]);
        }

        return null;
    }
}
