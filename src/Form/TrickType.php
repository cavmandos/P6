<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex : Method']
            ])
            ->add('groupId', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'label' => 'Choisissez une option',
                'choices' => [
                    'Grabs' => 'option1',
                    'Flips' => 'option2',
                    'Rails et Jibs' => 'option3',
                    'Spins' => 'option4',
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'ex : Un Method est un grab, pas une cuisine Ikea...']
            ])
            ->add('userId', HiddenType::class, [
                'data' => 'id de la personne'
            ])
            ->add('lastUpdate', HiddenType::class, [
                'data' => 'derniere mise à jour'
            ])

            // For medias
            ->add('medias', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre url ici'],
                'label' => 'URL du média (image ou vidéo)',
                'required' => false,
                'mapped' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
