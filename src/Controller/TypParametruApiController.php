<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 17.08.20
 * Time: 16:24
 */


namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Entity\TypParametru;
use App\Form\TypParametruType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TypParametruApiController extends BaseApiController
{
    /**
     * @Route("/Api/TypParametru/Dodaj", name="typ_parametru_dodaj_api", methods={"POST"})
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
        $typParametru = new TypParametru();
        $form = $this->createForm(TypParametruType::class, $typParametru, ["csrf_protection" => false]);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typParametru);
            $entityManager->flush();
            return new JsonResponse([
                'erros' => [],
                'data' => []
            ],200);
        }
        return new JsonResponse([
            'errors' => $form->getErrors(),
            'data' => []
        ], 200);
    }

    /**
     * @Route("/Api/TypParametru/Edytuj/{id}", name="typ_parametru_edytuj_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, TypParametru $typParametru)
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
        $form = $this->createForm(TypParametruType::class, $typParametru, ["csrf_protection" => false]);
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
        ], 200);
    }

    /**
     * @Route("/Api/TypParametru/Usun/{id}", name="typ_parametru_usun_api", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function usun(Request $request, EntityManagerInterface $entityManager, TypParametru $typParametru)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

        $entityManager->remove($typParametru);
        $entityManager->flush();
        return new JsonResponse([
            'errors' => [],
            'data' => []
        ], 200);
    }

    /**
     * @Route("/Api/TypParametru", name="typ_parametru_index_api", methods={"POST"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

        $typyParametrow = $entityManager->getRepository(TypParametru::class)->findAll();
        $return = [];
        foreach ($typyParametrow as $typ) {
            /** @var TypParametru $typ */
            $return[] = [
                'id' => $typ->getId(),
                'nazwa' => $typ->getNazwa(),
                'symbol' => $typ->getSymbol(),
                'jednostkaMiary' => $typ->getJednostkaMiary(),
                'typDanych' => $typ->getTypDanych(),
                'akceptowalneWartosci' => $typ->getAkceptowalneWartosci()
            ];
        }
        return new JsonResponse([
            'errors' => [],
            'data' => $return
        ], 200);
    }


    /**
     * @Route("/Api/TypParametru/TypyDanych", name="typ_parametru_typy_danych_api", methods={"POST"})
     */
    public function getTypyDanych(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'];
        $password = $data['credentials']['base64_password'];
        if(($code = $this->autoryzacja($login, $password)) !== true)
            return new JsonResponse( [
                'errors' => $code,
                'data' => []
            ], $code);

        $typyParametrow = $entityManager->getRepository(TypParametru::class)->findAll();
        $return = [];
        foreach ($typyParametrow as $typ) {
            /** @var TypParametru $typ */
            $return[] = [
                'typDanych' => $typ->getTypDanych(),
            ];
        }
        return new JsonResponse([
            'errors' => [],
            'data' => $return
        ], 200);
    }
}
