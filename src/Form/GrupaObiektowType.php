<?php

namespace App\Form;

use App\Entity\GrupaObiektow;
use App\Entity\TypParametru;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupaObiektowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('symbol', TextType::class, ['label' => 'Symbol'])
            ->add('nazwa', TextType::class, ['label' => 'Nazwa'])
            ->add('typyParametrow', EntityType::class, [
                'label' => 'Typy.parametrow',
                'multiple' => true,
                'class' => TypParametru::class,
                'choice_label' => 'nazwa'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GrupaObiektow::class,
            'translation_domain' => 'App'
        ]);
    }
}
