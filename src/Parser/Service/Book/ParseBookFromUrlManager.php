<?php

declare(strict_types=1);

namespace App\Parser\Service\Book;

use App\Author\Factory\AuthorFactoryInterface;
use App\Author\Repository\AuthorRepositoryInterface;
use App\Book\DTO\BookDTO;
use App\Book\Factory\BookFactoryInterface;
use App\Book\Repository\BookRepositoryInterface;
use App\Category\Factory\CategoryFactoryInterface;
use App\Category\Repository\CategoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class ParseBookFromUrlManager implements ParseBookFromUrlManagerInterface
{
    public function __construct(
        private ParseBookFromUrlInJsonServiceInterface $parseBookFromUrlServiceInJsonService,
        private LoggerInterface $logger,
        private ParameterBagInterface $parameterBag,
        private AuthorRepositoryInterface $authorRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private BookRepositoryInterface $bookRepository,
        private AuthorFactoryInterface $authorFactory,
        private CategoryFactoryInterface $categoryFactory,
        private BookFactoryInterface $bookFactory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function urlHandle(string $url, string $importFilePath, string $format, ?int $getBooks = null): ?array
    {
        if ('json' === $format) {
            $result = $this->parseBookFromUrlServiceInJsonService->urlHandle($url, $importFilePath, $getBooks);
        }

        return $result ?? null;
    }

    /**
     * @param array<string, mixed> $books
     */
    public function parse(ProgressBar $progressBar, array $books, int $batchSize, bool $dryRun = false): void
    {
        $i = 0;
        foreach ($books as $bookData) {
            $this->save($bookData, $dryRun);

            if ((++$i % $batchSize) === 0) {
                if (!$dryRun) {
                    $this->entityManager->flush();
                    // Не очищайте диспетчер сущностей, чтобы избежать проблем со ссылками на сущности
                }
                $this->logger->info(sprintf('Обработано %d книг', $i));
            }

            $progressBar->advance();
        }

        if (!$dryRun) {
            $this->entityManager->flush();
            // Не очищайте диспетчер сущностей, чтобы избежать проблем со ссылками на сущности
        }

        $this->logger->notice(sprintf('Импорт завершен. Обработано книг: %d', count($books)));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function save(array $data, bool $dryRun = false): void
    {
        $bookDTO = BookDTO::fromArray($data);

        if (empty($bookDTO->title)) {
            $this->logger->warning('Книга без названия, пропускаем', ['data' => $data]);

            return;
        }

        if (empty($bookDTO->isbn)) {
            $this->logger->warning('Книга без ISBN, пропускаем', ['data' => $data]);

            return;
        }

        $existingBook = $this->bookRepository->findOneBookBy(['isbn' => $bookDTO->isbn]);
        if ($existingBook) {
            $this->logger->info('Книга с ISBN {isbn} уже существует, обновляем', ['isbn' => $bookDTO->isbn]);
        } else {
            $this->logger->info('Создаем новую книгу с ISBN {isbn}', ['isbn' => $bookDTO->isbn]);
        }

        $book = $this->bookFactory->create($bookDTO, $existingBook, $dryRun);

        if (!$dryRun) {
            $this->entityManager->persist($book);
        }

        $this->logger->info('Книга сохранена', ['title' => $book->getTitle(), 'isbn' => $book->getIsbn()]);
    }

    /**
     * Удаление неявных дубликатов из массива строк.
     * Например: "Microsoft .NET" и "Microsoft.NET" будут считаться дубликатами.
     *
     * @param array<string> $items
     *
     * @return array<string>
     */
    private function removeImplicitDuplicates(array $items): array
    {
        $normalizedMap = [];
        $result = [];

        foreach ($items as $item) {
            $normalized = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $item));
            if (!isset($normalizedMap[$normalized])) {
                $normalizedMap[$normalized] = $item;
                $result[] = $item;
            }
        }

        return $result;
    }

    public function prepareAuthorsAndCategories(array $books, string $baseCategoryTitle, int $batchSize, bool $dryRun = false): void
    {
        $allIncomingAuthorsNames = [];
        $allCategories = [];

        foreach ($books as $bookData) {
            if (isset($bookData['authors']) && is_array($bookData['authors'])) {
                foreach ($bookData['authors'] as $authorName) {
                    if (!empty($authorName)) {
                        $allIncomingAuthorsNames[strtolower($authorName)] = $authorName;
                    }
                }
            }

            if (isset($bookData['categories']) && is_array($bookData['categories'])) {
                foreach ($bookData['categories'] as $categoryName) {
                    if (!empty($categoryName)) {
                        $allCategories[strtolower($categoryName)] = $categoryName;
                    }
                }
            }
        }

        if (!$dryRun) {
            $this->addDefaultCategory($baseCategoryTitle);

            $uniqueAuthors = $this->removeImplicitDuplicates(array_values($allIncomingAuthorsNames));
            $uniqueCategories = $this->removeImplicitDuplicates(array_values($allCategories));

            $this->upsertAuthors($uniqueAuthors, $batchSize);
            $this->upsertCategories($uniqueCategories, $batchSize);
        }
    }

    /**
     * @param array<string> $allIncomingAuthorsNames
     */
    public function upsertAuthors(array $allIncomingAuthorsNames, int $batchSize): void
    {
        $this->logger->info(sprintf('Обработка %d авторов', count($allIncomingAuthorsNames)));

        $uniqueIncomingAuthorNames = array_unique($allIncomingAuthorsNames);

        $existingAuthorsNames = $this->authorRepository->findExistingAuthorTitle($uniqueIncomingAuthorNames);

        $existingAuthorMap = [];
        if ($existingAuthorsNames) {
            foreach ($existingAuthorsNames as $authorTitle) {
                $existingAuthorMap[strtolower($authorTitle)] = $authorTitle;
            }
        }

        $newAuthorNames = [];
        foreach ($uniqueIncomingAuthorNames as $authorTitle) {
            if (!isset($existingAuthorMap[strtolower($authorTitle)])) {
                $newAuthorNames[] = $authorTitle;
            }
        }

        $this->logger->info(sprintf('Найдено %d существующих авторов, %d новых авторов',
            count($uniqueIncomingAuthorNames) - count($newAuthorNames),
            count($newAuthorNames)
        ));

        $batches = array_chunk($newAuthorNames, $batchSize);

        foreach ($batches as $batch) {
            foreach ($batch as $authorName) {
                $author = $this->authorFactory->create($authorName);
                $this->entityManager->persist($author);
            }

            $this->entityManager->flush();
            $this->logger->info(sprintf('Добавлено %d авторов', count($batch)));
        }
    }

    public function addDefaultCategory(string $baseCategoryTitle): void
    {
        $isExist = $this->categoryRepository->isExistCategoryByTitle($baseCategoryTitle);
        if (!$isExist) {
            $category = $this->categoryFactory->create($baseCategoryTitle);
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->logger->info(sprintf('Добавили базовую категорию "%s"', $baseCategoryTitle));
        }
    }

    /**
     * @param array<string> $allIncomingCategoryNames
     */
    public function upsertCategories(array $allIncomingCategoryNames, int $batchSize): void
    {
        $this->logger->info(sprintf('Обработка %d категорий', count($allIncomingCategoryNames)));

        $uniqueIncomingCategoryNames = array_unique($allIncomingCategoryNames);

        $existingCategoryNames = $this->categoryRepository->findExistingCategoryTitle($uniqueIncomingCategoryNames);

        $existingCategoryMap = [];
        if ($existingCategoryNames) {
            foreach ($existingCategoryNames as $categoryTitle) {
                $existingCategoryMap[strtolower($categoryTitle)] = $categoryTitle;
            }
        }

        $newCategoryTitles = [];
        foreach ($uniqueIncomingCategoryNames as $categoryTitle) {
            if (!isset($existingCategoryMap[strtolower($categoryTitle)])) {
                $newCategoryTitles[] = $categoryTitle;
            }
        }

        $this->logger->info(sprintf('Найдено %d существующих категорий, %d новых категорий',
            count($uniqueIncomingCategoryNames) - count($newCategoryTitles),
            count($newCategoryTitles)
        ));

        $batches = array_chunk($newCategoryTitles, $batchSize);

        foreach ($batches as $batch) {
            foreach ($batch as $categoryTitle) {
                $category = $this->categoryFactory->create($categoryTitle);
                $this->entityManager->persist($category);
            }

            $this->entityManager->flush();
            $this->logger->info(sprintf('Добавлено %d категорий', count($batch)));
        }
    }

    public function createTmpImportFilePath(): ?string
    {
        try {
            $appPublicUploadDir = $this->parameterBag->get('app.public_upload_dir');
            $appImportUploadDir = $this->parameterBag->get('app.import_upload_dir');

            $importDir = $appPublicUploadDir.$appImportUploadDir;
            if (!is_dir($importDir)) {
                mkdir($importDir, 0644, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $importFileName = "books_import_$timestamp.json";
            $importFilePath = $importDir.'/'.$importFileName;
        } catch (\Exception $e) {
            $this->logger->error('Ошибка: '.$e->getMessage(), ['exception' => $e]);
        }

        return $importFilePath ?? null;
    }

    public function deleteTmpImportFile(string $importFilePath): void
    {
        if (file_exists($importFilePath)) {
            unlink($importFilePath);
            $this->logger->info(sprintf('Удален временный файл импорта: %s', $importFilePath));
        }
    }
}
