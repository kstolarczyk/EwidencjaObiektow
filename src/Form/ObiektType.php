<?php

namespace App\Form;

use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObiektType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('symbol')
            ->add('nazwa')
            ->add('grupa', EntityType::class, [
        'label' => 'Grupa.Obiektow',
        'class' => GrupaObiektow::class,
        'choice_label' => 'nazwa'
    ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Obiekt::class,
        ]);
    }
}
