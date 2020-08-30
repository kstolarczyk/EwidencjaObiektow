<?php

namespace App\Controller;

use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use App\Entity\TypParametru;
use App\Form\ObiektType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ObiektController extends AbstractController
{
    /**
     * @Route("/", name="obiekt_index")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $grupaId = $request->query->getInt('grupaId', 0);
        $typyParametrow = [];
        if ($grupaId > 0) {
            $grupaObiektow = $entityManager->getRepository(GrupaObiektow::class)->find($grupaId);
            if ($grupaObiektow instanceof GrupaObiektow) {
                $typyParametrow = $grupaObiektow->getTypyParametrow();
            }
        }

        $viewData = ['grupaId' => $grupaId, 'typyParametrow' => $typyParametrow, 'maps_used' => true];
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($this->renderView('obiekt/tabela.ajax.html.twig', $viewData));
        }
        return $this->render('obiekt/index.html.twig', $viewData);
    }

    /**
     * @Route("/Obiekt/Ajax/{id}", name="obiekt_ajax_lista", condition="request.isXmlHttpRequest()")
     */
    public function listaObiektow(Request $request, EntityManagerInterface $entityManager,
                                  UserInterface $user, GrupaObiektow $grupaObiektow)
    {
        if (!$grupaObiektow->getUsers()->contains($user)) {
            throw new NotFoundHttpException();
        }
        $params = $request->query->all();
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
            $file = $obiekt->getImgFile();
            if ($file instanceof UploadedFile) {
                $newFileName = uniqid(md5($file->getClientOriginalName())) . "." . $file->getClientOriginalExtension();
                $path = $_ENV["IMG_DIR"];
                $file->move($path, $newFileName);
                $obiekt->setZdjecie($newFileName);
                $obiekt->setImgFile(null);
            }
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
     * @Route("/Obiekt/Edytuj/{id}", name="obiekt_edytuj", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"},
     *      methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, Filesystem $fileSystem, Obiekt $obiekt)
    {
        $form = $this->createForm(ObiektType::class, $obiekt);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $obiekt->getImgFile();
            if ($file instanceof UploadedFile) {
                $newFileName = uniqid(md5($file->getClientOriginalName())) . "." . $file->getClientOriginalExtension();
                $path = $_ENV["IMG_DIR"];
                $file->move($path, $newFileName);
                if ($obiekt->getZdjecie() !== null) {
                    $fileSystem->remove($path . $obiekt->getZdjecie());
                }
                $obiekt->setZdjecie($newFileName);
                $obiekt->setImgFile(null);
            }
            $entityManager->flush();
            return new JsonResponse(true);
        }

        return new JsonResponse($this->renderView('obiekt/form.html.twig', [
            'form' => $form->createView(),
            'enum_type' => TypParametru::ENUM
        ]));
    }

    /**
     * @Route("/Obiekt/Usun/{id}", name="obiekt_usun", condition="request.isXmlHttpRequest()", requirements={"id":"\d+"},
     *      methods={"POST"})
     */
    public function usun(EntityManagerInterface $entityManager, Filesystem $filesystem, Obiekt $obiekt)
    {
        $entityManager->remove($obiekt);
        $entityManager->flush();
        if ($obiekt->getZdjecie() != null && file_exists($path = $_ENV["IMG_DIR"] . $obiekt->getZdjecie())) {
            $filesystem->remove($path);
        }
        return new JsonResponse(true);
    }

    /**
     * @Route("/Obiekt/Mapa", name="obiekty_mapa", condition="request.isXmlHttpRequest()")
     * @IsGranted("ROLE_ADMIN")
     */
    public function obiekty(EntityManagerInterface $entityManager, Request $request)
    {
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
            'obiekty' => $obiekty,
            'coords' => ['lat' => (float)$_ENV['MAPS_DEFAULT_LAT'], 'lng' => (float)$_ENV['MAPS_DEFAULT_LON']],
            'zoom' => 10,
        ]);
    }

    /**
     * @Route("/Obiekt/Zdjecie/{id}", name="obiekt_zdjecie", condition="request.isXmlHttpRequest()",
     *     requirements={"id":"\d+"}, defaults={"id"=0})
     */
    public function zdjecie(Obiekt $obiekt)
    {
        $path = $_ENV["IMG_DIR"] . $obiekt->getZdjecie();
        if (!file_exists($path)) return new JsonResponse(false, 404);
        $file = new File($path);
        $data = base64_encode(file_get_contents($path));
        return new JsonResponse(['content' => $data, 'mime' => $file->getMimeType()]);
    }

}
