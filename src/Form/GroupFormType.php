<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder/*
        ->add('group', EntityType::class, options:[
            'class' => Group::class,
            'choice_label' => 'name',
            'mapped' => false,
            'multiple' =>true,
            'expanded' =>true,
        ])*/
        ->add('id', HiddenType::class, options: [
            'attr' => [
                'class' => 'group-id',
            ],
        ])
        ->add('name', HiddenType::class, options: [
            'attr' => [
                'class' => 'group-name',
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
