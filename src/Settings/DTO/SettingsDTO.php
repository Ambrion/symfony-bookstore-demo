<?php

declare(strict_types=1);

namespace App\Settings\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SettingsDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\NotBlank]
        public string $value,
    ) {
    }
}
