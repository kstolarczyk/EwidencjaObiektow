<?php

namespace App\Form;

use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use App\Entity\Parametr;
use App\Entity\TypParametru;
use App\Entity\User;
use App\Repository\GrupaObiektowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ObiektType extends AbstractType
{
    protected EntityManagerInterface $entityManager;
    private ?User $user;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('symbol', TextType::class, ['label' => 'Symbol'])
            ->add('nazwa', TextType::class, ['label' => 'Nazwa'])
            ->add('dlugosc', HiddenType::class, ['error_bubbling' => false])
            ->add('szerokosc', HiddenType::class)
            ->add('imgFile', FileType::class, ['label' => 'Zdjecie'])
            ->add('grupa', EntityType::class, [
                'label' => 'Grupa.Obiektow',
                'class' => GrupaObiektow::class,
                'query_builder' =>
                    fn(GrupaObiektowRepository $repository) => $repository->getGrupyByUser($this->user),
                'required' => false,
                'choice_label' => 'nazwa'
            ])
            ->add('parametry', CollectionType::class, [
                'label' => false,
                'entry_type' => ParametrType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => false
                ],
            ]);
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $obiekt = $event->getData();
            if (!$obiekt instanceof Obiekt) return;
            $grupa = $obiekt->getGrupa();
            if (!$grupa) return;
            foreach ($grupa->getTypyParametrow() as $typ) {
                if (!$typ instanceof TypParametru) continue;
                if ($obiekt->getParametry()->exists(
                    fn(int $i, Parametr $p) => $p->getTyp() !== null && $p->getTyp()->getId() === $typ->getId()
                )) continue;
                $parametr = new Parametr();
                $parametr->setObiekt($obiekt);
                $parametr->setTyp($typ);
                $obiekt->addParametry($parametr);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Obiekt::class,
            'translation_domain' => 'App'
        ]);
    }
}
