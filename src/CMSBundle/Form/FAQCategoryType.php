<?php

namespace App\CMSBundle\Form;

use App\CMSBundle\Entity\FAQCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FAQCategoryType extends AbstractType
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
                        "minMessage" => "Your name should be at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('sortNo', IntegerType::class, [
                "label" => "Sort No.",
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FAQCategory::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bundle_cmsbundle_faq_category';
    }

}
