<?php


namespace App\Form;


use App\Entity\Campus;
use App\Data\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('campus', EntityType::class, [
                'label' => false,
                'required' => false,
                //quelle est la classe à afficher ici ?
                'class' => Campus::class,
                //quelle propriété utiliser pour les <option> dans la liste déroulante ?
                'choice_label' => 'nom',
                'placeholder' => '--Choisir un campus--'
            ])
            ->add('dateDebut', DateType::class, [
                'label' => false,
                'html5' => true,
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'et',
                'html5' => true,
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sortie dont je suis l\'organisateur',
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sortie auxquelles je suis inscrit/e',
                'required' => false,
            ])
            ->add('notInscrit', CheckboxType::class, [
                'label' => 'Sortie auxquelles je ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('terminees', CheckboxType::class, [
                'label' => 'Sortie passées',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}