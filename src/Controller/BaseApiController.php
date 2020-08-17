<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Command\UserPasswordEncoderCommand;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BaseApiController extends AbstractController
{
    public function autoryzacja(string $base64_login, string $base64_password){
        $login = base64_decode($base64_login);
        $password = base64_decode($base64_password);
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['username' => $login]);
        if(!$user)
            return 404;
        if(password_verify($password, $user->getPassword()))
            return true;
        return 401;
    }


}