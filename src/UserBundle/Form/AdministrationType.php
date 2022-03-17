<?php

namespace App\UserBundle\Form;

use App\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdministrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordRequired = false;
        $passwordConstraints = [
            new Length([
                'min' => 6,
                'minMessage' => 'Your password should be at least {{ limit }} characters',
                'max' => 4096,
            ]),
            new NotBlank()
        ];
        if ($builder->getData()->getId() == null) {
            $passwordRequired = true;
            $passwordConstraints[] = new NotBlank([
                'message' => 'Please enter a password',
            ]);
        }
        $builder
            ->add('fullName', TextType::class, [
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
            ->add('email', EmailType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => User::GENDER_MALE,
                    'Female' => User::GENDER_FEMALE,
                ],
                "attr" => [
                    "class" => "form-control form-control-select2"
                ]
            ])
            ->add('phone', TelType::class, [
                "required" => false,
                'attr' => [
                    'placeholder' => '01xxxxxxxxx',
                    "class" => "form-control"
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => $passwordRequired,
                'attr' => [
                    'autocomplete' => 'new-password'
                ],
                'first_options' => [
                    'label' => 'Password',
                    'required' => $passwordRequired,
                    "attr" => [
                        "class" => "form-control"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'required' => $passwordRequired,
                    "attr" => [
                        "class" => "form-control"
                    ]
                ],
                'constraints' => $passwordConstraints,
            ])
            ->add('enabled', CheckboxType::class, array(
                'label' => 'Active',
                'label_attr' => [
                    "class" => "custom-control-label"
                ],
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ["Default"],
        ]);
    }
}
