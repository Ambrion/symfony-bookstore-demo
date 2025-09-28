<?php

namespace App\Contact\Repository;

use App\Contact\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository implements ContactRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @return int contact id
     */
    public function create(Contact $contact): int
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        return $contact->getId();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function findOneContactBy(array $data): ?Contact
    {
        return $this->findOneBy($data);
    }
}
