<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, ['label' => 'Nazwa.uzytkownika'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('plainPassword', RepeatedType::class,
                ['mapped' => false, 'type' => PasswordType::class,
                    'first_name' => 'pass',
                    'second_name' => 'confirm',
                    'first_options' => ['label' => 'Haslo'],
                    'second_options' => ['label' => 'Powtorz.haslo']
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['translation_domain' => 'App', 'data_class' => User::class]);
    }
}