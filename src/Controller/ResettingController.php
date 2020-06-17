<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\NewPasswordType;
use App\Form\PasswordRequestType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResettingController extends AbstractController
{
    /**
     * @Route("/Password/Request", name="password_request")
     */
    public function request(Request $request, EntityManagerInterface $entityManager,
                            MailerInterface $mailer, TranslatorInterface $translator, LoggerInterface $logger)
    {
        $form = $this->createForm(PasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!$user instanceof User) {
                $form->get('email')->addError(
                    new FormError($translator->trans('Nie.odnaleziono.uzytkownika.podany.email', [], 'App'))
                );
                return $this->render('resetting/request.html.twig', ['form' => $form->createView()]);
            }
            $token = bin2hex(random_bytes(48));
            $user->setPasswordRequestToken($token);
            $entityManager->flush();

            $message = new Email();
            $message->from('kamilinho20@gmail.com')
                ->to($email)
                ->subject($translator->trans('Password.request.subject', [], 'App'))
                ->html($this->renderView('resetting/email.html.twig',
                    ['reset_url' => $this->generateUrl('password_reset', ['passwordRequestToken' => $token], UrlGeneratorInterface::ABSOLUTE_URL)]
                ));
            try {
                $mailer->send($message);
                return $this->render('resetting/link.sent.html.twig', ['email' => $email]);
            } catch (TransportExceptionInterface $e) {
                $logger->error($e->getMessage());
                $this->addFlash('error', $translator->trans('email.sending.failure.message', [], 'App'));
            }
        }
        return $this->render('resetting/request.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/Password/Reset/{passwordRequestToken}", name="password_reset")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $encoder,
                          EntityManagerInterface $entityManager, User $user,
                          AppAuthenticator $authenticator, GuardAuthenticatorHandler $authenticatorHandler)
    {

        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPlainPassword();
            $user->setPassword($encoder->encodePassword($user, $plainPassword));
            $user->setPasswordRequestToken(null);
            $entityManager->persist($user);
            $entityManager->flush();

            return $authenticatorHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
        }

        return $this->render('resetting/reset.html.twig', ['form' => $form->createView()]);
    }
}