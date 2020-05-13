<?php

namespace App\Form;

use App\Entity\TypParametru;
use App\Form\DataTransformer\ArrayToChoicesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;

class TypParametruType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typyDanych = TypParametru::getTypyDanych();
        $builder
            ->add('symbol', TextType::class, ['label' => 'Symbol'])
            ->add('nazwa', TextType::class, ['label' => 'Nazwa'])
            ->add('typDanych', ChoiceType::class, ['label' => 'Typ.danych', 'choices' => array_combine($typyDanych, $typyDanych), 'choice_translation_domain' => 'TypDanych'])
            ->add('jednostkaMiary', TextType::class, ['label' => 'Jednostka.miary', 'required' => false])
            ->add('akceptowalneWartosci', ChoiceType::class, ['label' => 'Akceptowalne.wartosci', 'multiple' => true]);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $typ = $data['typDanych'];
            $akceptowalne = $data['akceptowalneWartosci'] ?? [];
            $form->add('akceptowalneWartosci', ChoiceType::class, [
                'label' => 'Akceptowalne.wartosci',
                'choices' => array_combine($akceptowalne, $akceptowalne),
                'multiple' => true,
                'constraints' => $typ === TypParametru::ENUM ?
                    [new NotBlank()] : [new EqualTo(['value' => [], 'message' => 'The field akceptowalneWartosci should be empty'])]
            ]);
        });
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $typ = $event->getData();
            $form = $event->getForm();
            if ($typ instanceof TypParametru && $typ->getTypDanych() === TypParametru::ENUM) {
                $choices = array_combine($typ->getAkceptowalneWartosci() ?? [], $typ->getAkceptowalneWartosci() ?? []);
                $form->add('akceptowalneWartosci', ChoiceType::class, [
                    'label' => 'Akceptowalne.wartosci',
                    'choices' => $choices,
                    'multiple' => true,
                    'constraints' => [new NotBlank()]
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypParametru::class,
            'translation_domain' => 'App'
        ]);
    }
}
