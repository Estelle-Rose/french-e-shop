<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=> 'Prénom',
                'constraints' => new Length(null,2,30),
                'attr'=> [ 'placeholder' => 'Merci de saisir votre prénom']
            ])
            ->add('lastname',TextType::class, [
                'label'=> 'Nom de famille',
                'constraints' => new Length(null,2,30),
                'attr'=> [ 'placeholder' => 'Merci de saisir votre nom de famille']
            ])
            ->add('email', EmailType::class, [
                'constraints' => new Length(null,null,60),
                'attr' => ['placeholder' => 'Merci de saisir votre adresse mail']
            ])
            ->add('password', RepeatedType::class, [
                'type'=>PasswordType::class,
                'invalid_message' => 'les deux mots de passe doivent être identiques',
                'required' => true,
                'label'=> 'Mot de passe',
                'first_options'=>['label'=>'Mot de passe','attr' => ['placeholder' => 'Merci de saisir votre mot de passe']],
                'second_options'=>['label'=>'Confirmation du mot de passe', 'attr' => ['placeholder' => 'Merci de confirmer votre mot de passe']]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => ['class' => 'btn-sm btn btn-block btn-info ']
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