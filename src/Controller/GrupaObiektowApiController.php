<?php

namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Form\GrupaObiektowType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GrupaObiektowApiController extends BaseApiController
{
    /**
     * @Route("/Api/GrupaObiektow", name="grupa_obiektow_api")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false,401,[
                'error' => "InvalidCredentials",
            ]);
        $lista = $entityManager->getRepository(GrupaObiektow::class)->findAll();
        foreach ($lista as $grupa) {
            /** @var GrupaObiektow $grupa */
            $return[] = [
                'id' => $grupa->getId(),
                'nazwa' => $grupa->getNazwa(),
                'symbol' => $grupa->getSymbol(),
                'typyParametrow' => $grupa->getTypyParametrow(),
                'obiekty' => $grupa->getObiekty(),
                'users' => $grupa->getUsers(),
            ];
        }
        return new JsonResponse([
            'lista' => $return
        ]);
    }

    /**
     * @Route("/Api/GrupaObiektow/Dodaj", name="grupa_obiektow_dodaj_api", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false,401,[
                'error' => "InvalidCredentials",
            ]);
        $grupa = new GrupaObiektow();
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(GrupaObiektowType::class, $grupa, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grupa);
            $entityManager->flush();
            return new JsonResponse(true,200);
        }
        return new JsonResponse(false, 402,[
            $form->getErrors()
        ]);
    }

    /**
     * @Route("/Api/GrupaObiektow/Edytuj/{id}", name="grupa_obiektow_edytuj_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, GrupaObiektow $grupa)
    {
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false,401,[
                'error' => "InvalidCredentials",
            ]);
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(GrupaObiektowType::class, $grupa, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return new JsonResponse(true,200);
        }

        return new JsonResponse(false, 402,[
            $form->getErrors()
        ]);
    }

    /**
     * @Route("/Api/GrupaObiektow/Usun/{id}", name="grupa_obiektow_usun_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(EntityManagerInterface $entityManager, GrupaObiektow $grupa)
    {
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false,401,[
                'error' => "InvalidCredentials",
            ]);
        if (!$grupa->getObiekty()->isEmpty()) {
            return new JsonResponse(false, 400);
        }
        if (!$grupa->getTypyParametrow()->isEmpty()) {
            $grupa->getTypyParametrow()->clear();
        }
        $entityManager->remove($grupa);
        $entityManager->flush();
        return new JsonResponse(true, 200);
    }



}