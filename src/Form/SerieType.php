<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plexId', TextType::class, [
                'nullable' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tvdbId', TextType::class, [
                'nullable' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', TextType::class, [
                'nullable' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('vfName', CheckboxType::class, [
                'nullable' => true,
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
