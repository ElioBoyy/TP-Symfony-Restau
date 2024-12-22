<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
                'attr' => ['placeholder' => 'Entrez le nom du restaurant'],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Adresse du restaurant'],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'attr' => ['placeholder' => 'Pays du restaurant'],
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => 'Code postal du restaurant'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => ['placeholder' => 'Numéro de téléphone'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Adresse email du restaurant'],
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

