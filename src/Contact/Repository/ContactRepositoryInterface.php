<?php

declare(strict_types=1);

namespace App\Contact\Repository;

use App\Contact\Entity\Contact;

interface ContactRepositoryInterface
{
    /**
     * @return int contact id
     */
    public function create(Contact $contact): int;

    /**
     * @param array<string, mixed> $data
     */
    public function findOneContactBy(array $data): ?Contact;
}
