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
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false,401,[
                'error' => "InvalidCredentials",
            ]);
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
        return new JsonResponse(true, 200,[
            'data' => $return
        ]);
    }
    /**
     * @Route("/Api/Obiekt/Mapa", name="obiekty_mapa_api", methods={"POST"})
     *
     */
    public function obiekty(EntityManagerInterface $entityManager, Request $request)
    {

        if(!$this->autoryzacja("", "")) {
            return new JsonResponse(false, 401);
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

        return new JsonResponse(true, 200,
            [
            'obiekty' => $obiekty,
            'coords' => ['lat' => (float)$_ENV['MAPS_DEFAULT_LAT'], 'lng' => (float)$_ENV['MAPS_DEFAULT_LON']],
            'zoom' => 10,
            ]);
    }
    /**
     * @Route("/Api/Obiekt/Usun/{id}", name="obiekt_usun_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(Request $request, EntityManagerInterface $entityManager, Obiekt $obiekt)
    {
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false, 401);
        $entityManager->remove($obiekt);
        $entityManager->flush();
        return new JsonResponse(true, 200);

    }
    /**
     * @Route("/Api/Obiekt/Edytuj/{id}", name="obiekt_edytuj_api", requirements={"id":"\d+"} , methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, Obiekt $obiekt)
    {
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false, 401);
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ObiektType::class, $obiekt, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return new JsonResponse(true, 200);
        }
        return new JsonResponse(false, 402,[
            $form->getErrors()
        ]);

    }
    /**
     * @Route("/Api/Obiekt/Dodaj", name="obiekt_dodaj_api", methods={"POST"})
     */
    public function dodaj(Request $request, EntityManagerInterface $entityManager)
    {
        if(!$this->autoryzacja("", ""))
            return new JsonResponse(false, 401);
        $obiekt = new Obiekt();
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ObiektType::class, $obiekt, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($obiekt);
            $entityManager->flush();
            return new JsonResponse(true, 200);
        }
        return new JsonResponse(false, 402, [
            $form->getErrors()
        ]);

    }
}