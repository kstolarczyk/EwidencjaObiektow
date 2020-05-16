<?php

namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use App\Entity\TypParametru;
use App\Form\ObiektType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ObiektController extends AbstractController
{
    /**
     * @Route("/Obiekt", name="obiekt_index")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $grupaId = $request->query->getInt('grupaId', 0);
        $lista = [];
        $typyParametrow = [];
        if ($grupaId > 0) {
            $grupaObiektow = $entityManager->getRepository(GrupaObiektow::class)->find($grupaId);
            if ($grupaObiektow instanceof GrupaObiektow) {
                $lista = $grupaObiektow->getObiekty();
                $typyParametrow = $grupaObiektow->getTypyParametrow();
            }

        }

        $viewData = ['lista' => $lista, 'grupaId' => $grupaId, 'typyParametrow' => $typyParametrow];
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($this->renderView('obiekt/tabela.ajax.html.twig', $viewData));
        }
        return $this->render('obiekt/index.html.twig', $viewData);
    }

    /**
     * @Route("/Obiekt/Ajax/{id}", name="obiekt_ajax_lista", condition="request.isXmlHttpRequest()")
     */
    public function listaObiektow(Request $request, EntityManagerInterface $entityManager, GrupaObiektow $grupaObiektow)
    {
        $params = $request->query->all();
        $total = 0;
        $filtered = 0;
        $lista = $entityManager->getRepository(Obiekt::class)
            ->dtFindBy(['grupa' => $grupaObiektow],
                $params['order'], $params['length'], $params['start'], $params['search']['value'], $total, $filtered);
        return new JsonResponse([
            'draw' => $params['draw'] + 1,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $lista->getValues()
        ]);
    }

    /**
     * @Route("/Obiekt/Dodaj", name="obiekt_dodaj", condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        $obiekt = new Obiekt();
        $form = $this->createForm(ObiektType::class, $obiekt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($obiekt);
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('obiekt/form.html.twig', [
            'form' => $form->createView(),
            'enum_type' => TypParametru::ENUM
        ]));
    }


    /**
     * @Route("/Obiekt/Edytuj/{id}", name="obiekt_edytuj", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, Obiekt $obiekt)
    {
        $form = $this->createForm(ObiektType::class, $obiekt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('obiekt/form.html.twig', [
            'form' => $form->createView(),
            'enum_type' => TypParametru::ENUM
        ]));
    }

    /**
     * @Route("/Obiekt/Usun/{id}", name="obiekt_usun", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(EntityManagerInterface $entityManager, Obiekt $obiekt)
    {
        $entityManager->remove($obiekt);
        $entityManager->flush();
        return new JsonResponse(true);
    }

}
