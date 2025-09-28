<?php

declare(strict_types=1);

namespace App\Mail\Service;

use App\Contact\DTO\ContactDTO;

interface SendContactEmailServiceInterface
{
    public function sendContactEmail(string $from, string $to, ContactDTO $contactDTO): void;
}
