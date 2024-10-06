<?php

// src/Form/EmailInvoiceType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailInvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('to', EmailType::class, [
                'label' => 'Destinataire',
                'required' => true,
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet de l\'email',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,  // Assurez-vous que la protection CSRF est activée
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'email_invoice',
        ]);
    }
}
