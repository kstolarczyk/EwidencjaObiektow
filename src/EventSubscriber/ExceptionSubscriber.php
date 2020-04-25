<?php


namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10]
            ]
        ];
    }

    public function processException(ExceptionEvent $exceptionEvent)
    {
        $exception = $exceptionEvent->getThrowable();
        $request = $exceptionEvent->getRequest();
        if ($exception instanceof AuthenticationException || $exception instanceof AccessDeniedException) {
            if ($request->isXmlHttpRequest()) {
                $exceptionEvent->setResponse(new Response('', 403));
            }
        }
    }
}