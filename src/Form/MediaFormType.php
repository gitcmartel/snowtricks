<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\tricks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MediaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('path', FileType::class, [
                'label' => 'Ajouter des images ou vidéos',
                'multiple' => false, 
                'mapped' => false, 
                'required' => false, 
                'attr' => [
                    'type' => 'file',
                    'accept' => '.png, .jpeg, .jpg, .svg, .mp4',
                    'class' => 'form-control'
                ]
            ])
            ->add('type', HiddenType::class)
            ->add('tricks', EntityType::class, [
                'class' => tricks::class,
                'choice_label' => 'id',
                'attr' => [
                    'hidden' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
