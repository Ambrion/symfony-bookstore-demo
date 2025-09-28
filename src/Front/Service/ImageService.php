<?php

declare(strict_types=1);

namespace App\Front\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class ImageService implements ImageServiceInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getImageUploadPathWithDomain(): string
    {
        $imageUploadDir = $this->parameterBag->get('app.image_upload_dir');

        $domain = $this->urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $domain.$imageUploadDir.'/';
    }
}
