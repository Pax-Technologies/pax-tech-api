<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('totalAmountNet')
            ->add('totalVatAmount')
            ->add('totalAmountGross')
            ->add('invoiceNumber')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Payé' => 1,
                    'Non payé' => 2,
                    'Annulé' => 3,
                    // autres options...
                ],
                'attr' => ['class' => 'form-control'],
                // définir le statut par défaut à Non payé
                'data' => 2
            ])
            ->add('client', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('invoiceDetails', CollectionType::class, [
                'entry_type' => InvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
