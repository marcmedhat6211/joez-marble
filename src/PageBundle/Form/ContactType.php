<?php

namespace App\PageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "attr" => [
                    'placeholder' => 'Name *',
                    "class" => "form-control"
                ],
                'constraints' => [
                    new NotBlank(["message" => "Please provide your name"]),
                ]
            ])
            ->add('email', EmailType::class, [
                "attr" => [
                    'placeholder' => 'Email Address *',
                    "class" => "form-control"
                ],
                'constraints' => [
                    new NotBlank(["message" => "Please provide a valid email"]),
                    new Email(["message" => "Your email doesn't seems to be valid"]),
                ]
            ])
            ->add('message', TextareaType::class, [
                "attr" => [
                    'placeholder' => 'Message *',
                    "rows" => 6,
                    "class" => "form-control"
                ],
                'constraints' => [
                    new NotBlank(["message" => "Please provide your message"]),
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'contact_form';
    }

}
