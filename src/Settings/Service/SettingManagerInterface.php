<?php

declare(strict_types=1);

namespace App\Settings\Service;

use App\Settings\DTO\SettingsDTO;

interface SettingManagerInterface
{
    public function findOneByName(string $name): ?SettingsDTO;
}
