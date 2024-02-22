<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\TricksGroup;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TricksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', HiddenType::class)
            ->add('name')
            ->add('description')
            ->add('image', FileType::class, [
                'multiple' => false, 
                'mapped' => false, 
                'required' => false, 
                'attr' => [
                    'accept' => '.png, .jpeg, .jpg, .svg',
                    'maxsize' => '2M', 
                    'class' => 'form-control edit-hero-image d-none'
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'attr' => [ 
                    'class' => 'form-control d-none'
                ]
            ])
            ->add('tricks_group', EntityType::class, [
                'class' => TricksGroup::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select' 
                ], 
                'label' => 'Groupe', 
                'label_attr' => [
                    'class' => 'input-group-text' 
                ],
            ])
            ->add('medias', CollectionType::class, [
                'entry_type' => MediaFormType::class, // Le type de formulaire pour chaque élément de la collection
                'allow_add' => true, // Autorisez l'ajout dynamique d'éléments à la collection
                'by_reference' => false, // Pour manipuler les objets directement, pas seulement les références
                'allow_delete' => true, // Autorise la suppression d'éléments de la collection
                'prototype' => true, // Activer le prototype pour les nouveaux éléments
                'prototype_name' => '__media_name__', // Le nom du prototype
                // Ajoutez d'autres options si nécessaire
            ])
            ->add('isHeroImageDeleted', CheckboxType::class, [
                'label' => false,
                'mapped' => false, // Ne pas mapper à une propriété de l'entité
                'required' => false,
                'data' => false,
                'attr' => [
                    'hidden' => true, // Rend le champ hidden
                    'class' => 'is-hero-image-deleted' // Ajoute une classe pour le cibler avec JavaScript
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
