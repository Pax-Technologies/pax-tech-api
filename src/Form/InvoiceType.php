<?php

namespace App\Form;

use App\Entity\Client;
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
            ->add('billingMonth', ChoiceType::class, [
                'choices' => [
                    'Janvier'   => 1,
                    'Février'   => 2,
                    'Mars'      => 3,
                    'Avril'     => 4,
                    'Mai'       => 5,
                    'Juin'      => 6,
                    'Juillet'   => 7,
                    'Août'      => 8,
                    'Septembre' => 9,
                    'Octobre'   => 10,
                    'Novembre'  => 11,
                    'Décembre'  => 12,
                ],
                'data' => date('n'),
                'mapped' => false,  // Ce champ est non mappé
            ])
            ->add('billingYear', ChoiceType::class, [
                'choices' => $this->generateArrayOfYears(),
                'data' => date('Y'),
                'mapped' => false,  // Ce champ est non mappé
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
                'class' => Client::class,
                'choice_label' => 'company',
            ])
            ->add('invoiceDetails', CollectionType::class, [
                'entry_type' => InvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' =>false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }

    public function generateArrayOfYears()
    {
        $years = range(date('Y'), date('Y')-10);
        return array_combine($years, $years);  // Générer un tableau de 10 dernières années
    }
}
