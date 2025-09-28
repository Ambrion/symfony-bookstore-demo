<?php

declare(strict_types=1);

namespace App\Mail\Service;

use App\Mail\DTO\EmailDTO;

interface EmailManagerInterface
{
    public function sendEmail(EmailDTO $emailDTO, bool $isHtml = false): void;
}
