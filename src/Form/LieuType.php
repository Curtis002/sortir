<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du lieu'
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue'
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'label' => 'Ville',
                'choice_label' => 'nom',
                'placeholder' => '-- Choisir une ville --',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
