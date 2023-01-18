<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\EventType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, 
        ['required' => false,
        ])
        ->add('dataType', TextType::class, 
        ['required' => false,
        ])
        ->add('createdAt')
        ->add('slug')
        ->add('updatedAt')
        ->add('eventType', EntityType::class, [
            'class' => EventType::class,
            'choice_label' => function ($eventType) {
                return $eventType->getName();
            },
            'expanded' => false,
            'multiple' => false,
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
