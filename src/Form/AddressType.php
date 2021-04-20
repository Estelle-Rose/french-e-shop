<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom de l'adresse",
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Maison',

                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',

                ]
            ])
            ->add('lastname',TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre nom',

                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Société',
                'required' => false,
                'attr' => [
                    'placeholder' => '(facultatif) Entrez le nom de votre société',

                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'placeholder' => '8 rue des tulipes',

                ]
            ])
            ->add('postcode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre code postal',

                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre ville',

                ]
            ])
            ->add('country' , CountryType::class, [
                'label' => 'Pays',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre pays',

                ]
            ])
            ->add('phone',TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone',

                ]
            ] )
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}