<?php

declare(strict_types=1);

namespace App\Contact\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email обязателен к заполнению!')]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank(message: 'Сообщение обязательно к заполнению!')]
        #[Assert\Type('string')]
        public string $message,
        #[Assert\Type('string')]
        public ?string $name = null,
        #[Assert\Type('string')]
        public ?string $phone = null,
    ) {
    }
}
