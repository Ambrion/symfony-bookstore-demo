<?php

namespace App\Category\Controller;

use App\Category\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Category>
 */
class AdminCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Категория')->setHelp('Основная информация'),
            TextField::new('title', 'Название'),
            AssociationField::new('parentId', 'Родительская категория')
                ->setCrudController(self::class)
                ->autocomplete(),
            SlugField::new('slug', 'ЧПУ')
                ->setTargetFieldName('title'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Категории')
            ->setPageTitle(Crud::PAGE_NEW, 'Создать новую категорию')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать категорию')
            ->setEntityLabelInSingular('Категория')
            ->setEntityLabelInPlural('Категории')
            ->setPaginatorPageSize(20);
    }
}
