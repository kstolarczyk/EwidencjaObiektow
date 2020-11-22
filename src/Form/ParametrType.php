<?php

namespace App\Form;

use App\Entity\Parametr;
use App\Entity\TypParametru;
use App\Form\DataTransformer\TypParametruTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            if ($data instanceof Parametr) {
                $typ = $data->getTyp();
                if ($typ instanceof TypParametru) {
                    $label = "{$typ->getNazwa()}";
                    $jm = $typ->getJednostkaMiary();
                    if ($jm) {
                        $label .= " [$jm]";
                    }
                    $this->dodajKontrolke($typ, $form, $label);
                }
            }
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            $typ = isset($data['typ']) ? $this->entityManager->getRepository(TypParametru::class)->find($data['typ']) : null;
            if ($typ instanceof TypParametru) {
                $label = "{$typ->getNazwa()}";
                $jm = $typ->getJednostkaMiary();
                if ($jm) {
                    $label .= " [$jm]";
                }
                $this->dodajKontrolke($typ, $form, $label);
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

    private function dodajKontrolke(TypParametru $typ, FormInterface $form, string $label)
    {
        switch ($typ->getTypDanych()) {
            case TypParametru::ENUM:
                $form->add('value', ChoiceType::class, [
                    'label' => $label,
                    'choices' => array_combine($typ->getAkceptowalneWartosci() ?? [], $typ->getAkceptowalneWartosci() ?? [])
                ]);
                break;
            case TypParametru::DATETIME:
                $form->add('value', DateTimeType::class, ['label' => $label, 'widget' => 'single_text',
                    'input_format' => 'yyyy-MM-dd HH:mm']);
                break;
            case TypParametru::DATE:
                $form->add('value', DateType::class, ['label' => $label, 'widget' => 'single_text', 'input_format' => 'yyyy-MM-dd']);
                break;
            case TypParametru::TIME:
                $form->add('value', TimeType::class, ['label' => $label, 'widget' => 'single_text', 'input_format' => 'HH:mm']);
                break;
            case TypParametru::STRING:
                $form->add('value', TextType::class, ['label' => $label]);
                break;
            case TypParametru::INT:
                $form->add('value', IntegerType::class, ['label' => $label]);
                break;
            case TypParametru::FLOAT:
                $form->add('value', NumberType::class, ['label' => $label, 'scale' => 2, 'html5' => true]);
                break;
        }
    }
}
