<?php

namespace App\CMSBundle\Form;

use App\CMSBundle\Entity\FAQ;
use App\CMSBundle\Entity\FAQCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FAQType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', TextareaType::class, [
                'required' => false,
                'label' => 'Question',
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
            ->add('answer', TextareaType::class, [
                'required' => false,
                'label' => 'Answer',
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
            ])
            ->add('publish', CheckboxType::class, [
                'label_attr' => [
                    "class" => "custom-control-label"
                ],
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ])
            ->add('faqCategory', EntityType::class, [
                'required' => true,
                'placeholder' => 'Choose an option',
                'class' => FAQCategory::class,
                "attr" => [
                    "class" => "form-control form-control-select2"
                ],
                "constraints" => [
                    new NotBlank(),
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('fc')
                        ->andWhere('fc.deleted IS NULL')
                        ->orderBy('fc.id', 'DESC');
                },
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FAQ::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bundle_cmsbundle_faq';
    }

}
