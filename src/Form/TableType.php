<?php

namespace App\Form;

use App\Entity\Plan;
use App\Entity\Reservation;
use App\Entity\Table;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('positionX')
            ->add('positionY')
            ->add('isActive')
            ->add('nbPersonneMax')
            ->add('tableNumber')
            ->add('plan', EntityType::class, [
                'class' => Plan::class,
                'choice_label' => 'id',
            ])
            ->add('reservations', EntityType::class, [
                'class' => Reservation::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Table::class,
        ]);
    }
}
