<?php

namespace App\CMSBundle\Form;

use App\CMSBundle\Entity\Banner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PN\LocaleBundle\Form\Type\TranslationsType;
use App\CMSBundle\Form\Translation\BannerTranslationType;
use PN\MediaBundle\Form\SingleImageType;

class BannerType extends AbstractType
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
            ])
            ->add('placement', ChoiceType::class, [
                'choices' => Banner::$placements,
                "attr" => [
                    "class" => "form-control form-control-select2"
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
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ])
            ->add('actionButton', TextType::class, [
                "required" => false,
                "label" => "Action Button Name",
                "attr" => [
                    "class" => "form-control"
                ],
            ])
            ->add('url', UrlType::class, [
                'required' => false
            ])
            ->add('text', TextareaType::class, [
                'required' => false,
                'label' => 'Banner Text',
                "attr" => [
                    "class" => "form-control"
                ],
            ])
            ->add('openNewTab', CheckboxType::class, [
                'label' => 'Open new tab',
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Banner::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'pn_bundle_cmsbundle_banner';
    }

}
