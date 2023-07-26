<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Container61FWced\getCategoryRepositoryService;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, array('class'=>'App\Entity\Category', 'choice_label'=>'cateName'))
            ->add('prodName', TextType::class)
            ->add('price', TextType::class)
            ->add('description', TextareaType::class)
            ->add('quantity', TextType::class)
            ->add('date', TextType::class)
            ->add('productImage', FileType::class, [
                'label' => 'Image file',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                  new File([
                      'maxSize' => '5M',
                      'mimeTypesMessage' => 'Please upload a valid image',
                  ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
