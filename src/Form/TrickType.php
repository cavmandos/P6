<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex : Method'],
                'constraints' => [new NotBlank()],
            ])
            ->add('groupId', EntityType::class, [
                'label' => 'Choisissez une option',
                'class' => Group::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select'],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'ex : Un Method est un grab, pas une cuisine Ikea...'],
                'constraints' => [new NotBlank()],
            ])

            ->add('medias', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre iframe ici'],
                'label' => 'Ajouter une vidéo',
                'required' => false,
                'mapped' => false,
            ])

            ->add('images', FileType::class, [
                 'attr' => ['class' => 'form-control', 'placeholder' => 'Sélectionnez votre image'],
                 'label' => 'Ajouter une image',
                 'required' => false,
                 'mapped' => false,
                 'multiple' => true
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
