<?php

namespace App\Form;

use App\Entity\EpisodeShow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpisodeShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('showDate', DateTimeType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tvdbId', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('saison', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('saisonNumber', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('episodeNumber', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('duration', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EpisodeShow::class,
        ]);
    }
}
