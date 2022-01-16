<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom',TextType::class,[
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Entrer ici votre prénom'
                ]
            ])
            ->add('nom',TextType::class,[
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Entrer ici votre prénom'
                ]
            ])
            ->add('email',EmailType::class,[
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Entrer ici votre nom'
                ]
            ])
            ->add('content',TextareaType::class,[
                'label' => 'Votre message',
                'attr' => [
                    'placeholder' => 'Entrer ici votre message'
                ]
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
