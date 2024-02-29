<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\GameDeveloper;
use App\Entity\GameGenre;
use App\Entity\GameMode;
use App\Entity\GamePlatform;
use App\Entity\GamePublishers;
use App\Entity\GameSerie;
use App\Entity\GameTheme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('releaseDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('developer', EntityType::class, [
                'class' => GameDeveloper::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('publishers', EntityType::class, [
                'class' => GamePublishers::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('modes', EntityType::class, [
                'class' => GameMode::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('platforms', EntityType::class, [
                'class' => GamePlatform::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('themes', EntityType::class, [
                'class' => GameTheme::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('genre', EntityType::class, [
                'class' => GameGenre::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('serie', EntityType::class, [
                'class' => GameSerie::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('valide', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-1'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
