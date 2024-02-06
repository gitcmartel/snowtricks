<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\TricksGroup;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TricksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('tricks_group', EntityType::class, [
                'class' => TricksGroup::class,
                'choice_label' => 'id',
            ])
            ->add('medias', FileType::class, [
                'label' => 'Ajouter des images ou vidéos',
                'multiple' => true, 
                'mapped' => false, 
                'required' => false
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
