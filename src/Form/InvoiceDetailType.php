<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\InvoiceDetail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', null, [
                'label' => false,
            ])
            ->add('unitPriceExcl', null, [
                'label' => false
            ])
            ->add('quantity', null, [
                'label' => false
            ])
            ->add('totalExcl', null, [
                'label' => false
            ])
            ->add('vatRate', null, [
                'label' => false
            ])
            ->add('totalIncl', null, [
                'label' => false
            ])
            ->add('periodicity', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    '' => null,
                    '1 an' => '1 year',
                    '6 mois' => '6 months',
                    '3 mois' => '3 months',
                    '1 mois' => '1 month'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceDetail::class,
        ]);
    }
}
