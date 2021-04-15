<?php
namespace App\Form;
use App\Classes\Search;
use App\Entity\Category;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $builder
        ->add('string', TextType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
            'placeholder' => 'votre recherche...',
            'class' => 'form-control-sm'
            ]
        ])
        ->add('categories', EntityType::class, [
            'label' => false,
            'required' => false,
            'class' => Category::class,
            'multiple' => true,
            'expanded' => true
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Rechercher',
            'attr' => ['class' => 'btn-block btn-sm btn btn-info  ']
        ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET', // Get pour que les utilisateurs puissent partager l'url d'une recherche
            'csrf_protection' => false // pas besoin de sécurité ici
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // retire le préfixe search de l'url
    }
}