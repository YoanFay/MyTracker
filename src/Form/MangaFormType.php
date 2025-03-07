<?php

namespace App\Form;

use App\Entity\Manga;
use App\Entity\MangaDesigner;
use App\Entity\MangaType;
use App\Entity\MangaAuthor;
use App\Entity\MangaEditor;
use App\Entity\MangaGenre;
use App\Entity\MangaTheme;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MangaFormType extends AbstractType
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
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('type', EntityType::class, [
                'class' => MangaType::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('genres', EntityType::class, [
                'class' => MangaGenre::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'select2 w-100'],
                'multiple' => true,
                'expanded' => false,
                'required' => false
            ])
            ->add('themes', EntityType::class, [
                'class' => MangaTheme::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'select2 w-100'],
                'multiple' => true,
                'expanded' => false,
                'required' => false
            ])
            ->add('author', EntityType::class, [
                'class' => MangaAuthor::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('designer', EntityType::class, [
                'class' => MangaDesigner::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('editor', EntityType::class, [
                'class' => MangaEditor::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr' => ['class' => 'btn btn-primary mt-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Manga::class,
        ]);
    }
}
