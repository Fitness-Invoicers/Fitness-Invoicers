<?php

namespace App\Form\Category;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alias', TextType::class, [
                'required' => false,
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Ex: poids-lourds'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
