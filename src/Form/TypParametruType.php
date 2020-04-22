<?php

namespace App\Form;

use App\Entity\TypParametru;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypParametruType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typyDanych = TypParametru::getTypyDanych();
        $builder
            ->add('symbol', TextType::class, ['label' => 'Symbol'])
            ->add('nazwa', TextType::class, ['label' => 'Nazwa'])
            ->add('typDanych', ChoiceType::class, ['label' => 'Typ.danych', 'choices' => array_combine($typyDanych, $typyDanych), 'choice_translation_domain' => 'TypDanych'])
            ->add('jednostkaMiary', TextType::class, ['label' => 'Jednostka.miary', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypParametru::class,
            'translation_domain' => 'App'
        ]);
    }
}
