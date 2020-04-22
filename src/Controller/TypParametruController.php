<?php

namespace App\Controller;

use App\Entity\TypParametru;
use App\Form\TypParametruType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TypParametruController extends BaseController
{
    /**
     * @Route("/TypParametru/Dodaj", name="typ_parametru_dodaj", condition="request.isXmlHttpRequest()")
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
        return new JsonResponse($this->renderView('typ_parametru/dodaj.html.twig', [
            'form' => $form->createView()
        ]));
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
}
