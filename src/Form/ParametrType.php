<?php

namespace App\Form;

use App\Entity\Parametr;
use App\Entity\TypParametru;
use App\Form\DataTransformer\TypParametruTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametrType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextType::class, ['label' => '__label__'])
            ->add('typ', HiddenType::class);
        $builder->get('typ')->addModelTransformer(new TypParametruTransformer($this->entityManager));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            $typ = $this->entityManager->getRepository(TypParametru::class)->find($data['typ']);
            if ($typ instanceof TypParametru) {
                $label = "{$typ->getNazwa()}";
                $jm = $typ->getJednostkaMiary();
                if ($jm) {
                    $label .= " [$jm]";
                }
                $form->add('value', TextType::class, ['label' => $label]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parametr::class,
            'translation_domain' => 'App'
        ]);
    }
}
