<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => "Date limite d'inscription"
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos'
            ])
//
//            ->add('campus',EntityType::class, [
//                'label' => 'Campus',
//                'class' => Campus::class,
//                'choice_label' => 'nom',
//                'placeholder' => '--Choisir un campus--'
//            ])
        ->add('ville', EntityType::class, [
            'label' => 'Ville',
            'mapped' => false,
            'class' => Ville::class,
            'choice_label' => 'nom'
            ])
        ->add('lieu', EntityType::class, [
            'label' => 'Lieu',
            'class' => Lieu::class,
            'choice_label' => 'nom'
        ])
        ->add('etatSortie', EntityType::class, [
                'label' => 'Etat',
                'class' => Etat::class,
                'choice_label' => 'libelle',
                'placeholder' => '--Choisir un état--'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
