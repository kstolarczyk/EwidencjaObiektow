<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

class BaseApiController extends AbstractController
{
    protected function autoryzacja(string $base64_login, string $base64_password)
    {
        $login = base64_decode($base64_login);
        $password = base64_decode($base64_password);
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['username' => $login]);
        if (!$user)
            return 404;
        if (password_verify($password, $user->getPassword()))
            return true;
        return 401;
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