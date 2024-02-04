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
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Anime' => 'Anime',
                    'Séries' => 'Séries',
                    'Replay' => 'Replay'
                ],
                'required' => true,
                'mapped' => false,
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
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control'],
                'mapped' => false
            ])
            ->add('valide', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
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
