<?php

namespace App\Parser\Command;

use App\Parser\Service\Book\ParseBookFromUrlManagerInterface;
use App\Settings\Service\SettingManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import-book-from-url',
    description: 'Импорт книг по URL',
)]
class ParseBookUrlCommand extends Command
{
    use LockableTrait;

    public function __construct(
        private readonly ParseBookFromUrlManagerInterface $parseBookFromUrlManager,
        private readonly SettingManagerInterface $settingManager,
        private readonly ParameterBagInterface $parameterBag,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::OPTIONAL, 'Введите URL для импорта книг')
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Введите формат импорта с URL, доступны: json')
            ->addOption('getBooks', null, InputOption::VALUE_OPTIONAL, 'Введите кол-во получаемых книг')
            ->addOption('batchSize', null, InputOption::VALUE_OPTIONAL, 'Введите размер очереди для сохранения книг')
            ->addOption('baseCategory', null, InputOption::VALUE_OPTIONAL, 'Введите название категории для книг без категории')
            ->addOption('dryRun', null, InputOption::VALUE_OPTIONAL, 'Парсинг без добавления в базу');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Начало парсинга данных...');

        if (!$this->lock()) {
            $output->writeln('Команда запущена и занята другим процессом!');

            return Command::SUCCESS;
        }

        $url = (string) $input->getArgument('url');
        $format = (string) $input->getOption('format');
        $getBooks = (int) $input->getOption('getBooks');
        $batchSize = (int) $input->getOption('batchSize');
        $baseCategoryTitle = (string) $input->getOption('baseCategory');
        $dryRun = (bool) $input->getOption('dryRun');

        if (empty($getBooks)) {
            $getBooks = null;
        }

        if (empty($url)) {
            $settingsUrl = null;
            $defaultUrl = $this->parameterBag->get('app.source_parsing_book_url');

            $setting = $this->settingManager->findOneByName('app.source_parsing_book_url');
            if (!empty($setting)) {
                $settingsUrl = $setting->value;
            }

            $url = $settingsUrl ?: $defaultUrl;
        }

        if (empty($baseCategoryTitle)) {
            $settingBaseCategory = null;
            $defaultBaseCategory = $this->parameterBag->get('app.base_category');

            $setting = $this->settingManager->findOneByName('app.base_category');
            if (!empty($setting)) {
                $settingBaseCategory = $setting->value;
            }

            $baseCategoryTitle = $settingBaseCategory ?: $defaultBaseCategory;
        }

        if (empty($batchSize)) {
            $settingsBatchSize = null;
            $defaultBatchSize = $this->parameterBag->get('app.item_import_batch_size');

            $setting = $this->settingManager->findOneByName('app.item_import_batch_size');
            if (!empty($setting)) {
                $settingsBatchSize = $setting->value;
            }

            $batchSize = $settingsBatchSize ?: $defaultBatchSize;
        }

        if (empty($format)) {
            $format = 'json';
        }

        $importFilePath = $this->parseBookFromUrlManager->createTmpImportFilePath();
        $books = $this->parseBookFromUrlManager->urlHandle($url, $importFilePath, $format, $getBooks);

        if (!empty($books)) {
            $this->parseBookFromUrlManager->prepareAuthorsAndCategories($books, $baseCategoryTitle, $batchSize);

            $progressBar = new ProgressBar($output, count($books));

            $progressBar->start();

            $this->parseBookFromUrlManager->parse($progressBar, $books, $batchSize, $dryRun);

            $progressBar->finish();

            $io->success('Парсинг данных успешно завершен!');
        } else {
            $io->success('Нет данных для импорта.');
        }

        $this->parseBookFromUrlManager->deleteTmpImportFile($importFilePath);
        $this->release();

        return Command::SUCCESS;
    }
}
