<?php


namespace App\Form;


use App\Entity\GrupaObiektow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    protected array $roles;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->roles = $parameterBag->get("security.role_hierarchy.roles");
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = [];
        array_walk($this->roles, function ($role, $key) use (&$roles) {
            $roles[$key] = $key;
        });
        $builder->add('roles', ChoiceType::class, [
            'label' => 'Role',
            'choices' => $roles,
            'multiple' => true,
            'choice_translation_domain' => false
        ])->add('grupyObiektow', EntityType::class, [
            'label' => 'Grupy.obiektow',
            'class' => GrupaObiektow::class,
            'choice_label' => 'nazwa',
            'multiple' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'App'
        ]);
    }
}