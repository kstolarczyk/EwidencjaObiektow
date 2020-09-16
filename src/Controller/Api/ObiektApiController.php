<?php


namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use App\Entity\Parametr;
use App\Entity\TypParametru;
use App\Form\ObiektType;
use App\Repository\ObiektRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ObiektApiController extends BaseApiController
{
    /**
     * @Route("/Api/Obiekt/Lista/{id}", name="obiekt_lista_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function index(Request $request, ObiektRepository $repository, GrupaObiektow $grupaObiektow)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'] ?? "";
        $password = $data['credentials']['base64_password'] ?? "";
        if(($auth = $this->autoryzacja($login, $password)) instanceof JsonResponse) {
            return $auth;
        }
        if(!$grupaObiektow->getUsers()->contains($auth)) {
            return new JsonResponse(['errors' => ['Access denied for this user!'], 'data' => []], 403);
        }
        $dateFrom = $data['lastUpdate'] ?? "1900-01-01 00:00";
        $lista = $repository->findFromDate($dateFrom, $grupaObiektow);
        return new JsonResponse([
            'errors' => [],
            'data' => $lista->getValues()
        ],200);
    }

    /**
     * @Route("/Api/Obiekt/Mapa", name="obiekty_mapa_api", methods={"POST"})
     *
     */
    public function obiekty(EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'] ?? "";
        $password = $data['credentials']['base64_password'] ?? "";
        if(($auth = $this->autoryzacja($login, $password)) instanceof JsonResponse) {
            return $auth;
        }

        $NELat = $request->query->get('NELat', 0.0);
        $NELng = $request->query->get('NELng', 0.0);
        $SWLat = $request->query->get('SWLat', 0.0);
        $SWLng = $request->query->get('SWLng', 0.0);

        $obiekty = $entityManager->getRepository(Obiekt::class)->findInBounds(
            $NELat,
            $NELng,
            $SWLat,
            $SWLng
        );

        return new JsonResponse([
            'errors' => [],
            'data' => [
                'obiekty' => $obiekty,
                'coords' => ['lat' => (float)$_ENV['MAPS_DEFAULT_LAT'], 'lng' => (float)$_ENV['MAPS_DEFAULT_LON']],
                'zoom' => 10,
            ]],200);
    }

    /**
     * @Route("/Api/Obiekt/Usun/{id}", name="obiekt_usun_api", requirements={"id":"\d+"}, methods={"DELETE"})
     */
    public function usun(Request $request, EntityManagerInterface $entityManager, Obiekt $obiekt)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'] ?? "";
        $password = $data['credentials']['base64_password'] ?? "";
        if(($auth = $this->autoryzacja($login, $password)) instanceof JsonResponse) {
            return $auth;
        }
        if($obiekt->getUser()->getId() != $auth->getId()) {
            return new JsonResponse(['data' => [], 'errors' => ['Access denied for this user!']], 403);
        }
        $obiekt->setUsuniety(true);
        $obiekt->setOstatniaAktualizacja(new \DateTime('now'));
        $entityManager->flush();
        return new JsonResponse([
            'errors' => [],
            'data' => []
        ], 200);

    }

    /**
     * @Route("/Api/Obiekt/Edytuj/{id}", name="obiekt_edytuj_api", requirements={"id":"\d+"} , methods={"PUT"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager,
                           ValidatorInterface $validator, Obiekt $obiekt)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'] ?? "";
        $password = $data['credentials']['base64_password'] ?? "";
        if(($auth = $this->autoryzacja($login, $password)) instanceof JsonResponse) {
            return $auth;
        }

        if($obiekt->getUser()->getId() != $auth->getId()) {
            return new JsonResponse(['data' => [], 'errors' => ['Access denied for this user!']], 403);
        }
        unset($data['credentials']);
        foreach ($data as $key => $value) {
            switch ($key) {
                case "grupa":
                    break;
                case "parametry":
                    if (!is_array($value)) break;
                    foreach ($value as $param) {
                        $typ = $entityManager->getRepository(TypParametru::class)->find($param["typ"] ?? 0);
                        $parametr = $entityManager->getRepository(Parametr::class)->findOneBy([
                            'typ' => $typ,
                            'obiekt' => $obiekt
                        ]);
                        if (!$parametr instanceof Parametr) continue;
                        $parametr->setValue((string)($param["value"] ?? null));
                        $params[] = $parametr;
                    }
                    break;
                default:
                    $obiekt->setPlainData($key, $value);
            }
        }

        $errors = $validator->validate($obiekt);
        if ($errors->count() <= 0) {
            $obiekt->setOstatniaAktualizacja(new \DateTime('now'));
            $entityManager->flush();
            return new JsonResponse([
                'errors' => [],
                'data' => [$obiekt]
            ], 200);
        }

        $err = [];
        foreach ($errors as $e) {
            if (!$e instanceof ConstraintViolation) continue;
            $err[$e->getPropertyPath()] = $e->getMessage();
        }
        return new JsonResponse(
            [
                'errors' => $err,
                'data' => []
            ], 400);

    }

    /**
     * @Route("/Api/Obiekt/Dodaj", name="obiekt_dodaj_api", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'] ?? "";
        $password = $data['credentials']['base64_password'] ?? "";
        if(($auth = $this->autoryzacja($login, $password)) instanceof JsonResponse) {
            return $auth;
        }

        unset($data['credentials']);
        $obiekt = new Obiekt();
        $form = $this->createForm(ObiektType::class, $obiekt, ["csrf_protection" => false]);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$obiekt->getGrupa()->getUsers()->contains($auth)) {
                return new JsonResponse(['data' => [], 'errors' => ['Access denied for this group!']], 403);
            }
            $obiekt->setOstatniaAktualizacja(new \DateTime('now'));
            $entityManager->persist($obiekt);
            $entityManager->flush();
            return new JsonResponse([
                'errors' => [],
                'data' => [$obiekt]
            ],200);
        }
        return new JsonResponse(
            [
                'errors' => $this->buildErrorArray($form),
                'data' => []
            ], 400);

    }
}