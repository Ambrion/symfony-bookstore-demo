<?php

namespace App\Author\Repository;

use App\Author\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository implements AuthorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param array<string> $titles
     *
     * @return array<string>
     */
    public function findExistingAuthorTitle(array $titles): array
    {
        $placeholders = array_map(function ($key) {
            return ':title_'.$key;
        }, array_keys($titles));

        $sql = 'SELECT title
                FROM author
                WHERE title IN ('.implode(', ', $placeholders).')
                ';

        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare($sql);

        foreach ($titles as $key => $title) {
            $stmt->bindValue(':title_'.$key, (string) $title, \PDO::PARAM_STR);
        }

        return $stmt->executeQuery()->fetchFirstColumn();
    }

    /**
     * @param array<string> $titles
     *
     * @return array<Author>
     */
    public function findExistingAuthorByTitle(array $titles): array
    {
        return $this->findBy(['title' => $titles]);
    }
}
