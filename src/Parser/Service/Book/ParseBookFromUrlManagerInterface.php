<?php

declare(strict_types=1);

namespace App\Parser\Service\Book;

use Symfony\Component\Console\Helper\ProgressBar;

interface ParseBookFromUrlManagerInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function urlHandle(string $url, string $importFilePath, string $format, ?int $getBooks = null): ?array;

    /**
     * @param array<string, mixed> $books
     */
    public function parse(ProgressBar $progressBar, array $books, int $batchSize, bool $dryRun = false): void;

    /**
     * @param array<string, mixed> $data
     */
    public function save(array $data, bool $dryRun = false): void;

    /**
     * @param array<string, mixed> $books
     */
    public function prepareAuthorsAndCategories(array $books, string $baseCategoryTitle, int $batchSize, bool $dryRun = false): void;

    /**
     * @param array<string> $allIncomingAuthorsNames
     */
    public function upsertAuthors(array $allIncomingAuthorsNames, int $batchSize): void;

    public function addDefaultCategory(string $baseCategoryTitle): void;

    /**
     * @param array<string> $allIncomingCategoryNames
     */
    public function upsertCategories(array $allIncomingCategoryNames, int $batchSize): void;

    public function createTmpImportFilePath(): ?string;

    public function deleteTmpImportFile(string $importFilePath): void;
}
