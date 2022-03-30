<?php

namespace App\ECommerceBundle\Form;

use App\ECommerceBundle\Controller\Administration\ProductController;
use App\ECommerceBundle\Entity\Category;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\Subcategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        "minMessage" => "Category's title should be at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('sku', TextType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                ]
            ])
            ->add('brief', TextareaType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        "minMessage" => "Category's title should be at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        "minMessage" => "Category's title should be at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('subcategory', EntityType::class, [
                'required' => true,
                'placeholder' => 'Choose an option',
                'class' => Subcategory::class,
                "attr" => [
                    "class" => "form-control form-control-select2"
                ],
                "constraints" => [
                    new NotBlank(),
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('sc')
                        ->orderBy('sc.id', 'DESC');
                },
            ])
            ->add('publish', CheckboxType::class, [
                'label_attr' => [
                    "class" => "custom-control-label"
                ],
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ])
            ->add('featured', CheckboxType::class, [
                'label_attr' => [
                    "class" => "custom-control-label"
                ],
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ])
            ->add('newArrival', CheckboxType::class, [
                'label_attr' => [
                    "class" => "custom-control-label"
                ],
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bundle_ecommercebundle_product';
    }

}
