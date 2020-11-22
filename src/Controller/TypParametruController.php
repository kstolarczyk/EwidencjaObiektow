<?php

namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Entity\TypParametru;
use App\Form\TypParametruType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TypParametruController extends BaseController
{
    /**
     * @Route("/TypParametru/Dodaj", name="typ_parametru_dodaj",
     *     condition="request.isXmlHttpRequest()", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        $typParametru = new TypParametru();
        $form = $this->createForm(TypParametruType::class, $typParametru);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $typParametru->setOstatniaAktualizacja(new \DateTime('now'));
            $entityManager->persist($typParametru);
            $entityManager->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse($this->renderView('typ_parametru/form.html.twig', [
            'form' => $form->createView(),
            'enum_type' => TypParametru::ENUM,
            'akceptowalne_hidden' => $typParametru->getTypDanych() !== TypParametru::ENUM
        ]));
    }

    /**
     * @Route("/TypParametru/Edytuj/{id}", name="typ_parametru_edytuj",
     *     condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, TypParametru $typParametru)
    {
        $form = $this->createForm(TypParametruType::class, $typParametru);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $typParametru->setOstatniaAktualizacja(new \DateTime('now'));
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('typ_parametru/form.html.twig', [
            'form' => $form->createView(),
            'enum_type' => TypParametru::ENUM,
            'akceptowalne_hidden' => $typParametru->getTypDanych() !== TypParametru::ENUM
        ]));
    }

    /**
     * @Route("/TypParametru/Usun/{id}", name="typ_parametru_usun",
     *      condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function usun(EntityManagerInterface $entityManager, TypParametru $typParametru)
    {
        $typParametru->setOstatniaAktualizacja(new \DateTime('now'));
        $typParametru->setUsuniety(true);
        $entityManager->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/TypParametru", name="typ_parametru_index")
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {
        $lista = $entityManager->getRepository(TypParametru::class)->findBy(['usuniety' => false]);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($this->renderView('typ_parametru/tabela.html.twig', [
                'lista' => $lista
            ]));
        }
        return $this->render('typ_parametru/index.html.twig', ['lista' => $lista]);
    }

    /**
     * @Route("/TypParametru/Ajax", name="typ_parametru_ajax", condition="request.isXmlHttpRequest()")
     */
    public function ajaxGet(EntityManagerInterface $entityManager, Request $request)
    {
        $grupaId = $request->query->getInt('grupaId', 0);
        $typyParametrow = [];
        if ($grupaId > 0) {
            $grupa = $entityManager->getRepository(GrupaObiektow::class)->find($grupaId);
            if ($grupa instanceof GrupaObiektow) {
                $typyParametrow = $grupa->getTypyParametrow()->filter(fn(TypParametru $typ) => !$typ->isUsuniety());
            }
        } else {
            $typyParametrow = $entityManager->getRepository(TypParametru::class)->findBy(['usuniety' => false]);
        }
        $return = [];
        foreach ($typyParametrow as $typ) {
            /** @var TypParametru $typ */
            $return[] = [
                'id' => $typ->getId(),
                'nazwa' => $typ->getNazwa(),
                'symbol' => $typ->getSymbol(),
                'jednostkaMiary' => $typ->getJednostkaMiary(),
                'typDanych' => $typ->getTypDanych(),
                'akceptowalneWartosci' => $typ->getAkceptowalneWartosci()
            ];
        }

        return new JsonResponse($return);
    }

    /**
     * @Route("/TypParametru/TypyDanych", name="typ_parametru_typy_danych", condition="request.isXmlHttpRequest()")
     */
    public function getTypyDanych()
    {
        return new JsonResponse(TypParametru::getTypyDanych());
    }
}
