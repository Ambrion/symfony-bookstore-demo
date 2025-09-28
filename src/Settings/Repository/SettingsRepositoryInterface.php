<?php

declare(strict_types=1);

namespace App\Settings\Repository;

use App\Settings\Entity\Settings;

interface SettingsRepositoryInterface
{
    public function findOneByName(string $name): ?Settings;
}
