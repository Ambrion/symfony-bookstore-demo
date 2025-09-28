<?php

declare(strict_types=1);

namespace App\Category\Factory;

use App\Category\Entity\Category;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFactory implements CategoryFactoryInterface
{
    public function __construct(public SluggerInterface $slugger)
    {
    }

    public function create(string $title, ?Category $parentId = null): Category
    {
        $category = new Category();

        $category->setTitle($title);

        $slug = $this->slugger->slug($title)->lower()->toString();
        $category->setSlug($slug);

        $category->setParentId($parentId);

        return $category;
    }
}
