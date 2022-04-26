<?php

namespace App\ECommerceBundle\Form;

use App\ECommerceBundle\Entity\Material;
use App\ECommerceBundle\Entity\Product;
use App\ECommerceBundle\Entity\Subcategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
            ->add('price', NumberType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ],
                "constraints" => [
                    new NotBlank(),
                    new GreaterThan(0)
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
                        ->andWhere('sc.deleted IS NULL')
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
            ->add('bestSeller', CheckboxType::class, [
                'label_attr' => [
                    "class" => "custom-control-label"
                ],
                "attr" => [
                    "class" => "custom-control-input"
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);
    }

    public function onPreSetData(FormEvent $event)
    {
        $entity = $event->getData();
        $form = $event->getForm();

        $materials = $entity->getMaterials();
        $this->addMaterialElements($form, $materials);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $materials = [];
        if (array_key_exists('materials', $data)) {
            foreach ($data['materials'] as $material) {
                $materials[] = $this->em->getRepository(Material::class)->find($material);
            }
        }

        $this->addMaterialElements($form, $materials);
    }

    public function onSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
//        $materials = $form->get("materials")->getData();
//        $errorMessage = "Please add at least one (1) material";
//
//        if (count($materials) == 0) {
//            $form->get("materials")
//                ->addError(new FormError($errorMessage));
//        }

        $title = $form->get("title")->getData();
        $otherProductWithSameSlug = $this->em->getRepository(Product::class)->findBy(["title" => $title, "deleted" => NULL]);
        $errorMessage = "Another product has the same title as this one, please choose another title for this product";
        if ($otherProductWithSameSlug)
        {
            $form->get("title")
                ->addError(new FormError($errorMessage));
        }
    }

    private function addMaterialElements(FormInterface $form, $materials = [])
    {
        $form->add('materials', EntityType::class, [
            'required' => true,
            'label' => "Materials",
            'multiple' => true,
            'placeholder' => 'Choose an option',
            'class' => Material::class,
            'choices' => $materials,
            'choice_label' => function ($material) {
                return $material->getTitle();
            },
            "attr" => [
                "class" => "select-search",
            ],
            "constraints" => [
                new NotBlank(),
            ]
        ]);
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
