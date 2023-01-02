<?php

namespace App\Form;

use App\Entity\Expense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            // ->add('slug')
            ->add('description')
            ->add('totalCost')
            ->add('totalPaid')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('eventList')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
        ]);
    }
}
