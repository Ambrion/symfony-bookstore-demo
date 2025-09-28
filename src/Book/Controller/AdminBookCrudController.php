<?php

namespace App\Book\Controller;

use App\Book\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimezoneField;

/**
 * @extends AbstractCrudController<Book>
 */
class AdminBookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $publicUploadDir = $this->getParameter('app.public_upload_dir');
        $imageUploadDir = $this->getParameter('app.image_upload_dir');

        return [
            FormField::addTab('Книга')->setHelp('Основная информация'),
            TextField::new('title', 'Название'),
            SlugField::new('slug', 'ЧПУ')
                ->setTargetFieldName('title'),
            TextField::new('isbn', 'ISBN (артикул)'),
            IntegerField::new('pageCount', 'Кол-во страниц'),
            ImageField::new('image')
                ->setBasePath($imageUploadDir)
                ->setUploadDir($publicUploadDir.$imageUploadDir)
                ->setUploadedFileNamePattern('[slug].[extension]')
                ->setFormTypeOptions([
                    'attr' => [
                        'accept' => 'image/*',
                    ],
                ])
                ->setLabel('Image'),
            TextEditorField::new('shortDescription', 'Короткое описание'),
            TextEditorField::new('longDescription', 'Полное описание'),
            DateTimeField::new('publishedDate', 'Дата публикации')
                ->renderAsNativeWidget(false)
                ->setFormTypeOptions([
                    'years' => range(1900, (int) date('Y') + 5),
                ]),
            TimezoneField::new('publishedTimeZone', 'Временная зона'),
            AssociationField::new('authors', 'Авторы')
                ->setFormTypeOptions([
                    'multiple' => true,
                    'by_reference' => false,
                ])
                ->autocomplete(),
            AssociationField::new('categories', 'Категории')
                ->setFormTypeOptions([
                    'multiple' => true,
                    'by_reference' => false,
                ])
                ->autocomplete(),
            ChoiceField::new('status', 'Статус')
                ->setChoices([
                    'DRAFT' => 'DRAFT',
                    'MEAP' => 'MEAP',
                    'PUBLISH' => 'PUBLISH',
                ]),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Книги')
            ->setPageTitle(Crud::PAGE_NEW, 'Создать новую книгу')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать книгу')
            ->setEntityLabelInSingular('Книгу')
            ->setEntityLabelInPlural('Книги')
            ->setPaginatorPageSize(20);
    }
}
