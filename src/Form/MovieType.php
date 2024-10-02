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
            ->add('tmdbId', IntegerType::class, [
                'label' => "ID TMDB",
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('showDate', DateTimeType::class, [
                'label' => 'Date de visionnage',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datetimepicker'],
                'html5' => false,
                'format' => 'dd/MM/YYYY HH:mm',
                'mapped' => false
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
