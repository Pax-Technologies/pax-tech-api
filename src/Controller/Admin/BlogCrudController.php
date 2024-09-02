<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Blog::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('subtitle'),
            DateField::new('creation_date'),
            TextEditorField::new('content'),
            ImageField::new('image_name')
                ->setBasePath('uploaded/images')
                ->setUploadDir('public/uploaded/images')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->setLabel('Image'),
            ImageField::new('cover_image_name')
                ->setBasePath('uploaded/covers')
                ->setUploadDir('public/uploaded/covers')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->setLabel('Cover'),
        ];
    }

}
