<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\tricks;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('creation_date')
            ->add('content', TextAreaType::class, [
                'attr' => [ 
                    'class' => 'form-control add-comment-text me-3',
                    'rows' => '5', 
                    'style' => 'height: 15vh;'
                ], 
                'label' => 'Comment', 
            ])
            ->add('tricks', EntityType::class, [
                'class' => tricks::class,
                'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => user::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
