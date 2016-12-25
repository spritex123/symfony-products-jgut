<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Product;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('placeholder' => 'Name')))
            ->add('description', TextType::class, array('attr' => array('placeholder' => 'Description')))
            ->add('thumbnail', FileType::class, array('data_class' => null, 'label' => 'Thumbnail (jpg, png)'))
            ->add('price', TextType::class, array('attr' => array('placeholder' => 'Price')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Product::class, 'validation_groups' => ['product']]);
    }
}
