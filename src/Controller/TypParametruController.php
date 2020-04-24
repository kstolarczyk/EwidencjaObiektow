<?php

namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Entity\TypParametru;
use App\Form\TypParametruType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TypParametruController extends BaseController
{
    /**
     * @Route("/TypParametru/Dodaj", name="typ_parametru_dodaj", condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        $typParametru = new TypParametru();
        $form = $this->createForm(TypParametruType::class, $typParametru);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typParametru);
            $entityManager->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse($this->renderView('typ_parametru/form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/TypParametru/Edytuj/{id}", name="typ_parametru_edytuj", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, TypParametru $typParametru)
    {
        $form = $this->createForm(TypParametruType::class, $typParametru);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('typ_parametru/form.html.twig', ['form' => $form->createView()]));
    }

    /**
     * @Route("/TypParametru/Usun/{id}", name="typ_parametru_usun", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(Request $request, EntityManagerInterface $entityManager, TypParametru $typParametru)
    {
        $entityManager->remove($typParametru);
        $entityManager->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/TypParametru", name="typ_parametru_index")
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {
        $lista = $entityManager->getRepository(TypParametru::class)->findAll();
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
                $typyParametrow = $grupa->getTypyParametrow();
            }
        } else {
            $typyParametrow = $entityManager->getRepository(TypParametru::class)->findAll();
        }
        $return = [];
        foreach ($typyParametrow as $typ) {
            /** @var TypParametru $typ */
            $return[] = [
                'id' => $typ->getId(),
                'nazwa' => $typ->getNazwa(),
                'symbol' => $typ->getSymbol(),
                'jednostkaMiary' => $typ->getJednostkaMiary(),
                'typDanych' => $typ->getTypDanych()
            ];
        }

        return new JsonResponse($return);
    }
}
