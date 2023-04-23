<?php

namespace App\Form;

use App\Entity\EventList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EventListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eventName', TextType::class)
            ->add('eventDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('eventBudget', TextType::class)
            ->add('eventLocation', TextType::class)
            ->add('image', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '900K',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ]
            ])
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('eventType')
            // ->add('client')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventList::class,
        ]);
    }
}
