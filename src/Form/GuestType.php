<?php

namespace App\Form;

use App\Entity\Guest;
use App\Entity\Tabletab;
use Doctrine\ORM\EntityRepository;
use App\Repository\TabletabRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                    'omnivore' => 'Omnivore',
                    'vegetarian' => 'Vegetarian',
                    'pescatarian' => 'Pescatarian',
                    'vegan' => 'Vegan',
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
                'query_builder' => function(EntityRepository $repo) use ($options) {
                    $qr = $repo->createQueryBuilder('t');
                    
                    if(!empty($options['eventListId'])) {
                        $qr->where('t.eventList = :eventListId')
                         ->setParameter('eventListId', $options['eventListId']);
                    }
                    return $qr;
                },
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
            'eventListId' => null
        ]);
    }
}
