<?php

namespace App\Form;

use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObiektType extends AbstractType
{

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('symbol', TextType::class, ['label' => 'Symbol'])
            ->add('nazwa', TextType::class, ['label' => 'Nazwa'])
            ->add('grupa', EntityType::class, [
                'label' => 'Grupa.Obiektow',
                'class' => GrupaObiektow::class,
                'choice_label' => 'nazwa'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Obiekt::class,
            'translation_domain' => 'App'
        ]);
    }
}
