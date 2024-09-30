<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\InvoiceDetail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', null, [
                'label' => false
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceDetail::class,
        ]);
    }
}
