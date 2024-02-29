<?php

namespace App\Form;

use App\Entity\GamePlatform;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('igdbId', IntegerType::class, [
                'label' => 'ID IGDB',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('platforms', EntityType::class, [
                'label' => 'Plateforme',
                'class' => GamePlatform::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('valide', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary mt-1'],
            ]);
    }
}
