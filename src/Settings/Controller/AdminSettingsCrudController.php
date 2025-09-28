<?php

namespace App\Settings\Controller;

use App\Settings\Entity\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Settings>
 */
class AdminSettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Settings::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Настройки')->setHelp('Перезаписывает базовые значения из .env'),
            TextField::new('name', 'Название'),
            TextField::new('value', 'Значение'),
            TextField::new('description', 'Описание'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Настройки системы')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавить новую настройку')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать настройку')
            ->setEntityLabelInSingular('Настройку')
            ->setEntityLabelInPlural('Настройки')
            ->setPaginatorPageSize(20);
    }
}
