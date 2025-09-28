<?php

declare(strict_types=1);

namespace App\Contact\Factory;

use App\Contact\DTO\ContactDTO;
use App\Contact\Entity\Contact;

interface ContactFactoryInterface
{
    public function create(ContactDTO $contactRequestDTO): Contact;
}
