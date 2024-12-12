<?php

namespace App\Form;

use App\Entity\Manga;
use App\Entity\MangaTome;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MangaTomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tomeNumber', IntegerType::class, [
                'label' => "Numéro du tome",
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lastTome', CheckboxType::class, [
                'label' => "Dernier tome du manga ?",
                'attr' => ['class' => 'form-check-input ms-2 mt-3'],
                'label_attr' => ['class' => 'my-2'],
                'required' => false
            ])
            ->add('page', IntegerType::class, [
                'label' => "Nombre de page",
                'attr' => ['class' => 'form-control'],
            ])
            ->add('releaseDate', DateType::class, [
                'label' => "Date de sortie",
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('readingStartDate', DateType::class, [
                'label' => "Date de début de lecture",
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('readingEndDate', DateType::class, [
                'label' => "Date de fin de lecture",
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('cover', TextType::class, [
                'label' => "URL de la couverture",
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('manga', EntityType::class, [
                'label' => "Manga",
                'class' => Manga::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr' => ['class' => 'btn btn-primary mt-1'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MangaTome::class,
        ]);
    }
}
