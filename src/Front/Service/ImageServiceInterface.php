<?php

declare(strict_types=1);

namespace App\Front\Service;

interface ImageServiceInterface
{
    public function getImageUploadPathWithDomain(): string;
}
