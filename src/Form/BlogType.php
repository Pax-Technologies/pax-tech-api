<?php

namespace App\Form;

use App\Entity\Blog;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titleFR', TextType::class, ['label' => 'Titre FR', 'attr' => ['class' => 'form-control']])
            ->add('titleEN', TextType::class, ['label' => 'Titre EN', 'attr' => ['class' => 'form-control']])
            ->add('creation_date', DateType::class, ['label' => 'Date de création', 'attr' => ['class' => 'form-control']])
            ->add('subtitleFR', TextType::class, ['label' => 'Sous-titre FR', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('subtitleEN', TextType::class, ['label' => 'Sous-titre EN', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('domainFR', TextType::class, ['label' => 'Domaine FR', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('domainEN', TextType::class, ['label' => 'Domaine EN', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('contentFR', CKEditorType::class, ['label' => 'Contenu FR', 'attr' => ['class' => 'form-control']])
            ->add('contentEN', CKEditorType::class, ['label' => 'Contenu EN', 'attr' => ['class' => 'form-control']])
            ->add('imageFile', DropzoneType::class, ['label'=> 'Image', 'mapped' => false,])
            ->add('source', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}