<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseApiController extends AbstractController
{
    protected function autoryzacja(string $base64_login, string $base64_password)
    {
        $login = base64_decode($base64_login);
        $password = base64_decode($base64_password);
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['username' => $login]);
        if($user instanceof User && password_verify($password, $user->getPassword())) {
            return $user;
        }
        return new JsonResponse(['errors' => ['Wrong credentials!'], 'data' => []], 401);
    }

    protected function buildErrorArray(FormInterface $form)
    {
        $errors = [];
        foreach ($form->all() as $child) {
            $errors = array_merge(
                $errors,
                $this->buildErrorArray($child)
            );
        }
        foreach ($form->getErrors() as $error) {
            $errors[$error->getCause()->getPropertyPath()] = $error->getMessage();
        }

        return $errors;
    }

}