<?php

namespace App\Form;

use App\Entity\Sweatshirt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SweatshirtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('price', NumberType::class)
            ->add('image', TextType::class)
            ->add('highlighted', CheckboxType::class, [
                'required' => false,
                'label' => 'Mis en avant ?'
            ])
            ->add('stockXS', NumberType::class)
            ->add('stockS', NumberType::class)
            ->add('stockM', NumberType::class)
            ->add('stockL', NumberType::class)
            ->add('stockXL', NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sweatshirt::class,
        ]);
    }
}
