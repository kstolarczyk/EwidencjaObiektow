<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class UserController extends BaseController
{
    private RoleHierarchyInterface $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @Route("/Users", name="user_index")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $lista = $entityManager->getRepository(User::class)->findAll();
        $viewData = ['users' => $lista];
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($this->renderView('user/tabela.html.twig', $viewData));
        }
        return $this->render('user/index.html.twig', $viewData);
    }

    /**
     * @Route("/Users/Edytuj/{id}", name="user_edytuj", requirements={"id":"\d+"}, defaults={"id"=0},
     *      condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function edytuj(Request $request, EntityManagerInterface $entityManager, User $user)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $form->get("roles")->getData();
            $user->setRoles($this->reduceRoleHierarchy($roles));
            $user->setGrupyObiektow($form->get("grupyObiektow")->getData());
            $entityManager->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse($this->renderView('user/form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/Users/Usun/{id}", name="user_usun", condition="request.isXmlHttpRequest()",
     *     methods={"POST"}, requirements={"id":"\d+"}, defaults={"id"=0})
     */
    public function usun(EntityManagerInterface $entityManager, User $user)
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/Users/Status/{id}", name="user_status", condition="request.isXmlHttpRequest()",
     *      methods={"POST"}, requirements={"id": "\d+"}, defaults={"id"=0})
     */
    public function zmienStatus(Request $request, EntityManagerInterface $entityManager, User $user)
    {
        $enabled = $request->request->getBoolean('enabled');
        $user->setEnabled($enabled);
        $entityManager->flush();
        return new JsonResponse(true);
    }

    private function reduceRoleHierarchy(array $roles)
    {
        $allRoles = $this->getParameter("security.role_hierarchy.roles");
        $tmp = $this->roleHierarchy->getReachableRoleNames($roles);
        $reachable = [];
        array_walk($tmp, function ($role) use (&$reachable) {
            $reachable[$role] = $role;
        });
        foreach ($reachable as $role) {
            if (!array_key_exists($role, $allRoles)) continue;
            array_walk($allRoles[$role], function ($r) use (&$reachable) {
                unset($reachable[$r]);
            });
        }
        return array_keys($reachable);
    }
}