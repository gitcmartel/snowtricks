<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\tricks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class MediaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('path', FileType::class, [
                'label' => 'Add images of video links',
                'multiple' => false, 
                'mapped' => false, 
                'required' => false, 
                'attr' => [
                    'type' => 'file',
                    'accept' => '.png, .jpeg, .jpg, .svg',
                    'class' => 'form-control w-90'
                ]
            ])
            ->add('link', TextType::class, [
                'label' => 'Paste a video link',
                'mapped' => false, 
                'required' => false,
                'attr' => [
                    'class' => 'd-none w-90 mb-3'
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
