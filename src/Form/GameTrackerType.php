<?php

namespace App\Form;

use App\Entity\GameSerie;
use App\Entity\GameTracker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameTrackerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('completeDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('endTime', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('completeTime', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('valide', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GameTracker::class,
        ]);
    }
}
