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
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

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
            'errors' => [],
            'data' => $return
        ], 200);
    }

    /**
     * @Route("/Api/GrupaObiektow/Dodaj", name="grupa_obiektow_dodaj_api", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

        unset($data['credentials']);
        $grupa = new GrupaObiektow();
        $form = $this->createForm(GrupaObiektowType::class, $grupa, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grupa);
            $entityManager->flush();
            return new JsonResponse([
                'errors' => [],
                'data' => []
            ],200);
        }
        return new JsonResponse(
            [
                'errors' => $form->getErrors(),
                'data' => []
            ], 402);
    }

    /**
     * @Route("/Api/GrupaObiektow/Edytuj/{id}", name="grupa_obiektow_edytuj_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, GrupaObiektow $grupa)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse([
                'errors' => $code,
                'data' => []
            ], $code);

        unset($data['credentials']);
        $form = $this->createForm(GrupaObiektowType::class, $grupa, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return new JsonResponse([
                'errors' => [],
                'data' => []
            ],200);
        }

        return new JsonResponse([
            'errors' => $form->getErrors(),
            'data' => []
        ], 402);
    }

    /**
     * @Route("/Api/GrupaObiektow/Usun/{id}", name="grupa_obiektow_usun_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(Request $request, EntityManagerInterface $entityManager, GrupaObiektow $grupa)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse([
                'errors' => $code,
                'data' => []
            ], $code);

        if (!$grupa->getObiekty()->isEmpty()) {
            return new JsonResponse([
                'errors' => [],
                'data' => []
            ], 400);
        }
        if (!$grupa->getTypyParametrow()->isEmpty()) {
            $grupa->getTypyParametrow()->clear();
        }
        $entityManager->remove($grupa);
        $entityManager->flush();
        return new JsonResponse([
            'errors' => [],
            'data' => []
        ], 200);
    }



}