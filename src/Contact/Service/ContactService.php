<?php

declare(strict_types=1);

namespace App\Contact\Service;

use App\Contact\Entity\Contact;
use App\Contact\Repository\ContactRepositoryInterface;

readonly class ContactService implements ContactServiceInterface
{
    public function __construct(private ContactRepositoryInterface $contactRepository)
    {
    }

    /**
     * @return int contact id
     */
    public function createContact(Contact $contact): int
    {
        return $this->contactRepository->create($contact);
    }
}
