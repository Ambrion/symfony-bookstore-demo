<?php

declare(strict_types=1);

namespace App\Mail\Message;

readonly class SendContactEmailMessage
{
    public function __construct(
        private int $id,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
