<?php

namespace App\CMSBundle\Form;

use App\CMSBundle\Entity\Testimonial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TestimonialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', TextType::class, [
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
            ])
            ->add('publish', CheckboxType::class, [
                'label_attr' => [
                    "class" => "custom-control-label"
                ],
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ])
            ->add('url', UrlType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        "minMessage" => "Message should be at least {{ limit }} characters"
                    ])
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'label_attr' => [
                    "class" => "custom-file-label"
                ],
                "attr" => [
                    "class" => "custom-file-input"
                ]
            ])
            ->add('socialMediaType', ChoiceType::class, [
                'choices' => Testimonial::$socialMediaTypes,
                "attr" => [
                    "class" => "form-control form-control-select2"
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
            'data_class' => Testimonial::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'bundle_cmsbundle_testimonial';
    }

}
