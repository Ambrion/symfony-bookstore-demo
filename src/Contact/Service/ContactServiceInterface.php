<?php

declare(strict_types=1);

namespace App\Contact\Service;

use App\Contact\Entity\Contact;

interface ContactServiceInterface
{
    /**
     * @return int contact id
     */
    public function createContact(Contact $contact): int;
}
