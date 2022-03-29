<?php

namespace App\ECommerceBundle\Form;

use App\ECommerceBundle\Entity\Currency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class CurrencyType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                ]
            ])
            ->add('egpEquivalence', NumberType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0,
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                    new GreaterThan([
                        "value" => 0
                    ])
                ]
            ])
            ->add('flag', FileType::class, [
                'mapped' => false,
                'label_attr' => [
                    "class" => "custom-file-label"
                ],
                "attr" => [
                    "class" => "custom-file-input"
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
            'data_class' => Currency::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bundle_ecommercebundle_currency';
    }

}
