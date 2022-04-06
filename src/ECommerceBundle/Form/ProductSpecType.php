<?php

namespace App\ECommerceBundle\Form;

use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\ProductSpec;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductSpecType extends AbstractType
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
                        "minMessage" => "Spec title should be at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('value', TextType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        "minMessage" => "Spec value should be at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('product', EntityType::class, [
                'required' => true,
                'placeholder' => 'Choose an option',
                'class' => Product::class,
                "attr" => [
                    "class" => "form-control form-control-select2"
                ],
                "constraints" => [
                    new NotBlank(),
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('sc')
                        ->andWhere('sc.deleted IS NULL')
                        ->orderBy('sc.id', 'DESC');
                },
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductSpec::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bundle_ecommercebundle_product-spec';
    }

}
