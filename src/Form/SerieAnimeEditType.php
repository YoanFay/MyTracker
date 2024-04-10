<?php

namespace App\Form;

use App\Entity\Serie;
use App\Entity\AnimeGenre;
use App\Entity\AnimeTheme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieAnimeEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tvdbId', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('serieType', EntityType::class, [
                'label' => 'Catégorie',
                'class' => \App\Entity\SerieType::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('animeGenres', EntityType::class, [
                'class' => AnimeGenre::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('animeThemes', EntityType::class, [
                'class' => AnimeTheme::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('valide', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary mt-1'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
