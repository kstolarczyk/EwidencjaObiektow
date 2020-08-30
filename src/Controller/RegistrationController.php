<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationType;
use App\Security\AppAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                             GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //TODO: make it configurable with APP_ENV (first ever user should be enabled automatically)

            $entityManager = $this->getDoctrine()->getManager();
            $firstUser = $entityManager->getRepository(User::class)->count([]) == 0;
            if ($firstUser) {
                $user->setRoles(['ROLE_SUPER_ADMIN']);
                $user->setEnabled(true);
            } else {
                $user->setEnabled($_ENV["USER_DEFAULT_ENABLED"] == "true");
            }

            $entityManager->persist($user);
            $entityManager->flush();

            if (!$user->isEnabled()) {
                throw new CustomUserMessageAuthenticationException("User %username% is disabled. Please contact with system administrator.", ['%username%' => $user->getUsername()]);
            }
            return $guardHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
