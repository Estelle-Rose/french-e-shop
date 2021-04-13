<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=> 'Prénom',
                'attr'=> [ 'placeholder' => 'Merci de saisir votre prénom']
            ])
            ->add('lastname',TextType::class, [
                'label'=> 'Nom de famille',
                'attr'=> [ 'placeholder' => 'Merci de saisir votre nom de famille']
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Merci de saisir votre adresse mail']
            ])
            ->add('password', RepeatedType::class, [
                'type'=>PasswordType::class,
                'invalid_message' => 'les deux mots de passe doivent être identiques',
                'required' => true,
                'label'=> 'Mot de passe',
                'attr'=> [ 'placeholder' => 'Merci de saisir votre mot de passe'],
                'first_options'=>['label'=>'Mot de passe'],
                'second_options'=>['label'=>'Confirmation du mot de passe']
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
