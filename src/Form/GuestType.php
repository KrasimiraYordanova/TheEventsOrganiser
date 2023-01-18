<?php

namespace App\Form;

use App\Entity\Guest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Tabletab;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('rdsvp', ChoiceType::class, [
                'choices' => [
                    'attending' => 'Attending',
                    'declined' => 'Declined',
                ],
                'expanded' => true,
                'multiple'=>false,
                'required' => false
            ])
            ->add('address')
            ->add('email')
            ->add('phone')
            ->add('diet', ChoiceType::class, [
                'choices' => [
                    'Omnivore' => 'Omnivore',
                    'Vegetarian' => 'Vegetarian',
                    'Prescetarian' => 'Prescetarian',
                    'Vegan' => 'Vegan',
                ],
                'expanded' => false,
                'multiple'=>false,
                'required' => false
            ])
            // ->add('token')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('eventList')
            ->add('tabletab', EntityType::class, [
                'class' => Tabletab::class,
                'choice_label' => function ($table) {
                    return $table->getName();
                },
                'expanded' => false,
                'multiple' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guest::class,
        ]);
    }
}
