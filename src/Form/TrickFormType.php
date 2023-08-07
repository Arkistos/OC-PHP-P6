<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\VideoFormType;
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
                'label'=>'Nom',
            ])
            ->add('description', TextareaType::class)
            /*->add('group', CollectionType::class, [
                'by_reference' => false,
                'entry_type' => GroupFormType::class,
                'allow_add' =>true
            ])*
            ->add('group', EntityType::class, options:[
                'class' => Group::class,
                'choice_label' => 'name',
                'mapped' => false,
                'multiple' =>true,
                'expanded' =>true,
            ])*/
            ->add('group', CollectionType::class, [
                'entry_type' => GroupFormType::class,
                'label' => 'Catégorie',
                'entry_options' => ['label'=>false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false

            ])
            ->add('pictures', FileType::class, [
                'label' => 'Images',
                'multiple' =>true,
                'mapped' => false,
                'required' => false,
            ])
            /*->add('videos', TextareaType::class, [
                'label' => 'Vidéo',
                'mapped' => false,
                'required' => false,
            ])*/
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoFormType::class,
                'label' => 'Videos',
                'entry_options' => ['label'=>false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
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
