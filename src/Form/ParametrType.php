<?php

namespace App\Form;

use App\Entity\Parametr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextType::class, ['label' => false]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $parametr = $event->getData();
            $form = $event->getForm();
            if ($parametr instanceof Parametr) {
                $form->add('value', TextType::class, ['label' => $parametr->getTyp()->getNazwa()]);
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
