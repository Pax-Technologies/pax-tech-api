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
            ->add('description')
            ->add('unitPriceExcl')
            ->add('quantity')
            ->add('totalExcl')
            ->add('vatRate')
            ->add('totalIncl')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceDetail::class,
        ]);
    }
}
