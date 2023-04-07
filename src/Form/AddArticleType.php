<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Titre',
            'constraints' => new Length([
                'min' => 2,
                'max' => 100
            ]),
            'attr' => [
                'placeholder' => "Titre de l'article",
                'class' => 'form-control'
            ]
        ])
        ->add('body', TextareaType::class, [
            'label' => 'Contenu',
            'constraints' => new Length([
                'min' => 2,
                'max' => 20000
            ]),
            'attr' => [
                'placeholder' => "Contenu de l'article",
                'class' => 'form-control'
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => "Valider",
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
