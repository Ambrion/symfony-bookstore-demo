<?php

declare(strict_types=1);

namespace App\Settings\Service;

use App\Settings\DTO\SettingsDTO;
use App\Settings\Repository\SettingsRepositoryInterface;

readonly class SettingManager implements SettingManagerInterface
{
    public function __construct(private SettingsRepositoryInterface $repository)
    {
    }

    public function findOneByName(string $name): ?SettingsDTO
    {
        $data = $this->repository->findOneByName($name);
        if (empty($data)) {
            return null;
        }

        return new SettingsDTO(name: $data->getName(), value: $data->getValue());
    }
}
