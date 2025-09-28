<?php

namespace App\Category\Repository;

use App\Category\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function isExistCategoryByTitle(string $title): int
    {
        $sql = 'SELECT COUNT(1)
                FROM category
                WHERE title = :title
        ';

        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare($sql);

        $stmt->bindValue('title', $title, \PDO::PARAM_STR);

        return (int) $stmt->executeQuery()->fetchOne();
    }

    /**
     * @param array<string> $titles
     *
     * @return array<string>
     *
     * @throws Exception
     */
    public function findExistingCategoryTitle(array $titles): array
    {
        if (empty($titles)) {
            return [];
        }

        $placeholders = array_map(function ($key) {
            return ':title_'.$key;
        }, array_keys($titles));

        $sql = 'SELECT title
                FROM category
                WHERE title IN ('.implode(', ', $placeholders).')
                ';

        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare($sql);

        foreach ($titles as $key => $title) {
            $stmt->bindValue(':title_'.$key, $title, \PDO::PARAM_STR);
        }

        return $stmt->executeQuery()->fetchFirstColumn();
    }

    /**
     * @param array<string> $titles
     *
     * @return array<Category>|null
     */
    public function findExistingCategoryByTitle(array $titles): ?array
    {
        return $this->findBy(['title' => $titles]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function getCurrentCategoryBy(array $data): Category
    {
        return $this->findOneBy($data);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return Category[]|null
     */
    public function findAllCategoriesBy(array $data): ?array
    {
        return $this->findBy($data);
    }
}
