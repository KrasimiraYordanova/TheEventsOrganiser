<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('namePath')
            // ->add('slug')
            ->add('album', ChoiceType::class, [
                'choices' => [
                    'Pre Event Photos' => 'Pre Event Photos',
                    'Event Photos' => 'Event Photos'
                ],
                'expanded' => false,
                'multiple'=>false,
            ])
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('eventList')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
