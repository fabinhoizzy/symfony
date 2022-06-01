<?php

namespace App\EventListeners;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryException;
use App\Kernel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
       return [
         KernelEvents::EXCEPTION => [
             ['handlerEntityException', 1],
             ['handler404Exception', 0],
          ],
       ];
    }

    public function handler404Exception(ExceptionEvent $event)
    {
        if ($event->getThrowable() instanceof NotFoundHttpException) {
            $response = HypermidiaResponse::fromError($event->getThrowable())->getResponse();
            $response->setStatusCode(Response::HTTP_NOT_FOUND); //404
            $event->setResponse($response);
        }
    }

    public function handlerEntityException(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof EntityFactoryException) {
            $response = HypermidiaResponse::fromError($event->getThrowable())->getResponse();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST); //400
            $event->setResponse($response);
        }
    }
}