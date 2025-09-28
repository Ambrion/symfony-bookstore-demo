<?php

namespace App\Book\Repository;

use App\Book\Entity\Book;
use App\Category\Entity\Category;
use App\Front\Filter\BookFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository implements BookRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function findOneBookBy(array $data): ?Book
    {
        return $this->findOneBy($data);
    }

    /**
     * @param Category[] $categories
     * @param array<int> $exceptBookIds
     *
     * @return Book[]
     */
    public function findByCategoriesWithLimit(array $categories, int $limit, array $exceptBookIds = []): array
    {
        $qb = $this->createQueryBuilder('b');
        $qb->join('b.categories', 'c')
           ->where($qb->expr()->in('c.id', ':categories'))
           ->setParameter('categories', array_map(fn ($category) => $category->getId(), $categories))
           ->setMaxResults($limit);

        if (!empty($exceptBookIds)) {
            $qb->andWhere($qb->expr()->notIn('b.id', ':exceptBookIds'))
               ->setParameter('exceptBookIds', $exceptBookIds);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Category[] $categories
     *
     * @return Book[]
     */
    public function findByCategoriesPaginated(array $categories, int $limit, int $offset, ?BookFilter $filter = null): array
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('DISTINCT b')
           ->join('b.categories', 'c')
           ->where($qb->expr()->in('c.id', ':categories'))
           ->setParameter('categories', array_map(fn ($category) => $category->getId(), $categories));

        $orConditions = [];

        if (!empty($filter) && !empty($filter->getTitle())) {
            $orConditions[] = $qb->expr()->like('b.title', ':title');
            $qb->setParameter('title', '%'.$filter->getTitle().'%');
        }

        if (!empty($filter) && !empty($filter->getAuthor())) {
            $qb->leftJoin('b.authors', 'a');
            $orConditions[] = $qb->expr()->like('a.title', ':author');
            $qb->setParameter('author', '%'.$filter->getAuthor().'%');
        }

        if (!empty($filter) && !empty($filter->getStatus())) {
            $orConditions[] = $qb->expr()->eq('b.status', ':status');
            $qb->setParameter('status', $filter->getStatus());
        }

        if (!empty($orConditions)) {
            $qb->andWhere($qb->expr()->orX(...$orConditions));
        }

        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Category[] $categories
     */
    public function countByCategories(array $categories, ?BookFilter $filter = null): int
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('COUNT(DISTINCT b.id)')
           ->join('b.categories', 'c')
           ->where($qb->expr()->in('c.id', ':categories'))
           ->setParameter('categories', array_map(fn ($category) => $category->getId(), $categories));

        $orConditions = [];

        if (!empty($filter) && !empty($filter->getTitle())) {
            $orConditions[] = $qb->expr()->like('b.title', ':title');
            $qb->setParameter('title', '%'.$filter->getTitle().'%');
        }

        if (!empty($filter) && !empty($filter->getAuthor())) {
            $qb->leftJoin('b.authors', 'a');
            $orConditions[] = $qb->expr()->like('a.title', ':author');
            $qb->setParameter('author', '%'.$filter->getAuthor().'%');
        }

        if (!empty($filter) && !empty($filter->getStatus())) {
            $orConditions[] = $qb->expr()->eq('b.status', ':status');
            $qb->setParameter('status', $filter->getStatus());
        }

        if (!empty($orConditions)) {
            $qb->andWhere($qb->expr()->orX(...$orConditions));
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
