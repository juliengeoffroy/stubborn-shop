<?php

namespace App\Form;

use App\Entity\Sweatshirt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SweatshirtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control']
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix (€)',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('image', TextType::class, [
                'label' => 'Nom du fichier image (ex: monimage.jpg)',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('isFeatured', CheckboxType::class, [
                'required' => false,
                'label' => 'Mettre en avant',
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('stockXS', NumberType::class, [
                'label' => 'Stock XS',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('stockS', NumberType::class, [
                'label' => 'Stock S',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('stockM', NumberType::class, [
                'label' => 'Stock M',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('stockL', NumberType::class, [
                'label' => 'Stock L',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('stockXL', NumberType::class, [
                'label' => 'Stock XL',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sweatshirt::class,
        ]);
    }
}
