<?php

namespace App\Form\Category;

use App\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorySearchAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alias', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('company', EntityType::class,
                [
                    'class' => Company::class,
                    'query_builder' => static function (EntityRepository $er) {
                        return $er->createQueryBuilder('c');
                    },
                    'placeholder' => 'Toutes',
                    'choice_label' => 'name',
                    'label' => 'Entreprise',
                    'attr'=>[
                        'class' => 'select2'
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
