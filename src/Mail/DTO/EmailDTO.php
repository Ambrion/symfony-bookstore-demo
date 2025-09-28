<?php

declare(strict_types=1);

namespace App\Mail\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmailDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $from,
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $to,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $subject,
        #[Assert\Type('string')]
        public ?string $text = null,
        #[Assert\Type('string')]
        public ?string $html = null,
    ) {
    }
}
