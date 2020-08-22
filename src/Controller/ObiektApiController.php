<?php


namespace App\Controller;

use App\Controller\BaseApiController;
use App\Entity\Parametr;
use App\Form\ObiektType;
use App\Form\ParametrType;
use App\Kernel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ObiektApiController extends BaseApiController
{
    /**
     * @Route("/Api/Obiekt/Lista/{id}", name="obiekt_lista_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager, GrupaObiektow $grupaObiektow)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

        $lista = $entityManager->getRepository(Obiekt::class)->findBy(['grupa' => $grupaObiektow]);
        $return = [];
        foreach ($lista as $obiekt) {
            /** @var Obiekt $obiekt */
            $return[] = [
                'id' => $obiekt->getId(),
                'nazwa' => $obiekt->getNazwa(),
                'symbol' => $obiekt->getSymbol(),
                'parametry' => $obiekt->getParametry(),
                'dlugosc' => $obiekt->getDlugosc(),
                'szerokosc' => $obiekt->getSzerokosc(),
            ];
        }
        return new JsonResponse([
            'errors' => [],
            'data' => []
        ],200);
    }
    /**
     * @Route("/Api/Obiekt/Mapa", name="obiekty_mapa_api", methods={"POST"})
     *
     */
    public function obiekty(EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

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
     * @Route("/Api/Obiekt/Usun/{id}", name="obiekt_usun_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(Request $request, EntityManagerInterface $entityManager, Obiekt $obiekt)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

        $entityManager->remove($obiekt);
        $entityManager->flush();
        return new JsonResponse([
            'errors' => [],
            'data' => []
        ],200);;

    }
    /**
     * @Route("/Api/Obiekt/Edytuj/{id}", name="obiekt_edytuj_api", requirements={"id":"\d+"} , methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, Obiekt $obiekt)
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
        $form = $this->createForm(ObiektType::class, $obiekt, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/Api/Obiekt/Dodaj", name="obiekt_dodaj_api", methods={"POST"})
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
        $obiekt = new Obiekt();
        $form = $this->createForm(ObiektType::class, $obiekt, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($obiekt);
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
}