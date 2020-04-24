<?php

namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use App\Form\ObiektType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ObiektController extends AbstractController
{
    /**
     * @Route("/obiekt", name="obiekt_index", methods={"GET"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $listaGrupObiektow = $entityManager->getRepository(GrupaObiektow::class)->findAll();
        $grupaId=$request->query->get('id');
        if($grupaId>0){
            $grupaObiektow=$entityManager->getRepository(GrupaObiektow::class)->find($grupaId);
            $lista=$grupaObiektow->getObiekty();
            return $this->render('obiekt/tabela.html.twig', [
                'lista' => $lista,
            ]);
        }


        $lista = $entityManager->getRepository(Obiekt::class)->findAll();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($this->renderView('obiekt/tabela.html.twig', ['lista' => $lista]));
        }
        return $this->render('obiekt/index.html.twig', [
            'lista' => $lista,
            'listaGrupObiektow' => $listaGrupObiektow,
        ]);
    }

    /**
     * @Route("/obiekt/Dodaj", name="obiekt_dodaj", condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        $obiekt = new Obiekt();
        //$obiekt->setParametry(null);
        $form = $this->createForm(ObiektType::class, $obiekt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($obiekt);
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('obiekt/form.html.twig', ['form' => $form->createView()]));
    }


    /**
     * @Route("/obiekt/Edytuj/{id}", name="obiekt_edytuj", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, Obiekt $obiekt)
    {
        $form = $this->createForm(Obiekt::class, $obiekt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('obiekt/form.html.twig', ['form' => $form->createView()]));
    }



}
