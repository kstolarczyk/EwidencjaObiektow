<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 13.09.20
 * Time: 16:51
 */

namespace App\Controller\Api;


use App\Controller\BaseApiController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserApiController extends BaseApiController
{
    /**
     * @Route("/Api/User", name="user_api", methods={"POST"})
     */
    public function index(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $login = $data['credentials']['base64_login'] ?? "";
        $password = $data['credentials']['base64_password'] ?? "";
        if(($auth = $this->autoryzacja($login, $password)) instanceof JsonResponse) {
            return $auth;
        }

        return new JsonResponse(['errors' => [], 'data' => [$auth]], 200);
    }
}