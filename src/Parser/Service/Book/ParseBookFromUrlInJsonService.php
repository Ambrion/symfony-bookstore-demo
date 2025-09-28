<?php

declare(strict_types=1);

namespace App\Parser\Service\Book;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class ParseBookFromUrlInJsonService implements ParseBookFromUrlInJsonServiceInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function urlHandle(string $url, string $importFilePath, ?int $getBooks = null): array
    {
        $this->logger->notice('Получаем книги', ['url' => $url, 'format' => 'json', 'getBooks' => $getBooks]);

        try {
            $client = HttpClient::create();

            $response = $client->request(
                'GET',
                $url
            );

            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode) {
                throw new \Exception('Не могу получить данные с URL');
            }

            $booksData = $response->getContent();
            file_put_contents($importFilePath, $booksData);

            $this->logger->notice(sprintf('Данные сохранены в файл: %s', $importFilePath));

            $books = json_decode($booksData, true);

            $this->logger->notice(sprintf('Получил книг для импорта: %d единицы', count($books)));

            if (null !== $getBooks) {
                $books = array_slice($books, 0, $getBooks);
            }
        } catch (\Exception $e) {
            $this->logger->error('Ошибка: '.$e->getMessage(), ['exception' => $e]);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Ошибка транспорта: '.$e->getMessage(), ['exception' => $e]);
        }

        return $books ?? [];
    }
}
