<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du film',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('showDate', DateTimeType::class, [
                'label' => 'Date de visionnage',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tmdbId', IntegerType::class, [
                'label' => "ID TMDB",
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('duration', IntegerType::class, [
                'label' => "Durée de l'épisode",
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary mt-1'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
