<?php

namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Form\GrupaObiektowType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GrupaObiektowController extends AbstractController
{
    /**
     * @Route("/GrupaObiektow", name="grupa_obiektow_index")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $lista = $entityManager->getRepository(GrupaObiektow::class)->findAll();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($this->renderView('grupa_obiektow/tabela.html.twig', ['lista' => $lista]));
        }
        return $this->render('grupa_obiektow/index.html.twig', [
            'lista' => $lista
        ]);
    }

    /**
     * @Route("/GrupaObiektow/Dodaj", name="grupa_obiektow_dodaj", condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        $grupa = new GrupaObiektow();
        $form = $this->createForm(GrupaObiektowType::class, $grupa);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grupa);
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('grupa_obiektow/form.html.twig', ['form' => $form->createView()]));
    }

    /**
     * @Route("/GrupaObiektow/Edytuj/{id}", name="grupa_obiektow_edytuj", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, GrupaObiektow $grupa)
    {
        $form = $this->createForm(GrupaObiektowType::class, $grupa);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('grupa_obiektow/form.html.twig', ['form' => $form->createView()]));
    }

    /**
     * @Route("/GrupaObiektow/Usun/{id}", name="grupa_obiektow_usun", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(EntityManagerInterface $entityManager, GrupaObiektow $grupa)
    {
        $entityManager->remove($grupa);
        $entityManager->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/GrupaObiektow/Ajax", name="grupa_obiektow_ajax", condition="request.isXmlHttpRequest()")
     */
    public function ajaxGet(EntityManagerInterface $entityManager)
    {
        $grupyObiektow = $entityManager->getRepository(GrupaObiektow::class)->findAll();
        $return = [];
        foreach ($grupyObiektow as $grupa) {
            /** @var GrupaObiektow $grupa */
            $return[] = [
                'id' => $grupa->getId(),
                'nazwa' => $grupa->getNazwa(),
                'symbol' => $grupa->getSymbol()
            ];
        }
        return new JsonResponse($return);
    }
}
