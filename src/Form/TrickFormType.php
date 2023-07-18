<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\DBAL\Types\StringType;
use Monolog\Handler\GroupHandler;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:[
                'label'=>'Nom'
            ])
            ->add('description')
            /*->add('group', CollectionType::class, [
                'by_reference' => false,
                'entry_type' => GroupFormType::class,
                'allow_add' =>true
            ])*/
            ->add('group', EntityType::class, options:[
                'class' => Group::class,
                'choice_label' => 'name',
                'mapped' => false,
                'multiple' =>true,
                'expanded' =>true,
            ])
            ->add('pictures', FileType::class, [
                'label' => false,
                'multiple' =>true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('videos', TextareaType::class, [
                'label' => 'Vidéo',
                'mapped' => false,
                'required' => false,
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
