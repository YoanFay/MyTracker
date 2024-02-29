<?php

namespace App\Form;

use App\Entity\Manga;
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
            ->add('nbTomes', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', EntityType::class, [
                'class' => MangaType::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('genres', EntityType::class, [
                'class' => MangaGenre::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('themes', EntityType::class, [
                'class' => MangaTheme::class, // Entité source
                'choice_label' => 'name', // Propriété de l'entité à afficher dans la liste déroulante
                'choice_attr' => ['class' => 'mx-2'],
                'attr' => ['class' => 'mx-2'],
                'multiple' => true, // Permet la sélection multiple
                'expanded' => true, // Affiche les options sous forme de cases à cocher
                'required' => false
            ])
            ->add('author', EntityType::class, [
                'class' => MangaAuthor::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('editor', EntityType::class, [
                'class' => MangaEditor::class,
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
            'data_class' => Manga::class,
        ]);
    }
}
