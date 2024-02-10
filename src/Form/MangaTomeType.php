<?php

namespace App\Form;

use App\Entity\Manga;
use App\Entity\MangaTome;
use Symfony\Component\Form\AbstractType;
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
                'attr' => ['class' => 'form-control'],
            ])
            ->add('page', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('releaseDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datepicker']
            ])
            ->add('readingStartDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datepicker'],
                'required' => false
            ])
            ->add('readingEndDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datepicker'],
                'required' => false
            ])
            ->add('cover', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('manga', EntityType::class, [
                'class' => Manga::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MangaTome::class,
        ]);
    }
}
