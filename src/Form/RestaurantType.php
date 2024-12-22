<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du restaurant',
            ])
            ->add('shortSummary', TextareaType::class, [
                'label' => 'Résumé court',
                'required' => true,
            ])
            ->add('longSummary', TextareaType::class, [
                'label' => 'Description détaillée',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}

