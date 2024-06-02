<?php

namespace App\Form;

use App\Entity\EpisodeShow;
use App\Entity\Serie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpisodeShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('showDate', DateTimeType::class, [
                'label' => 'Date de visionnage',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'html5' => false,
                'format' => 'dd/MM/YYYY HH:mm',
            ])
            ->add('name', TextType::class, [
                'label' => "Nom de l'épisode",
                'attr' => ['class' => 'form-control'],
            ])
            /*->add('saison', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])*/
            ->add('saisonNumber', IntegerType::class, [
                'label' => 'Numéro de la saison',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('episodeNumber', IntegerType::class, [
                'label' => "Numéro de l'épisode",
                'attr' => ['class' => 'form-control'],
            ])
            ->add('duration', IntegerType::class, [
                'label' => "Durée de l'épisode",
                'attr' => ['class' => 'form-control'],
            ])
            ->add('serie', EntityType::class, [
                'label' => "Série",
                'class' => Serie::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tvdbId', IntegerType::class, [
                'label' => "ID TVDB",
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary mt-1'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EpisodeShow::class,
        ]);
    }
}
