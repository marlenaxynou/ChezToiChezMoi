<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\All;


class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['maxlength' => 150]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('prix', IntegerType::class, [
                'label' => 'Prix'
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('nb_personne', IntegerType::class, [
                'label' => 'Nombre de personnes'
            ])
            ->add('disponibilite', DateTimeType::class, [
                'label' => 'Disponibilité',
                'widget' => 'single_text'
            ])
            ->add('images', FileType::class, [
                'label' => 'Images',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'constraints' => [
                    new All([ 
                        new Image([
                            'maxSize' => '5M',
                            'mimeTypes' => ['image/jpeg', 'image/png'],
                            'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG)'
                        ])
                    ])
                ]
                        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
