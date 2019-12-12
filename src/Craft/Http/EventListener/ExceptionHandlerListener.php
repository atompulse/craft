<?php

namespace Craft\Http\EventListener;

use Craft\Messaging\Http\HttpStatusCodes;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ExceptionHandlerListener
 * @package Craft\Http\EventListener
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ExceptionHandlerListener
{
    /**
     * @var bool
     */
    protected $isProduction;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, string $env)
    {
        $this->logger = $logger;
        $this->isProduction = !($env === 'dev' || $env === 'test');
    }

    /**
     * Intercept Exceptions and send valid ajax responses
     *
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        // Get the exception object from the event
        $error = $event->getThrowable();

        // Store logs
        $this->logger->error("Unexpected error occurred", [$error]);

        if ($error instanceof AccessDeniedException) {
            $status = HttpStatusCodes::AUTHORIZATION_ERROR;
        } else {
            if ($error instanceof NotFoundHttpException) {
                $status = HttpStatusCodes::INVALID_RESOURCE_ERROR;
            } else {
                $status = HttpStatusCodes::UNEXPECTED_ERROR;
            }
        }

        $data = [
            'status' => HttpStatusCodes::getName($status)
        ];

        if (!$this->isProduction) {
            $data['debug'] = [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'trace' => $error->getTraceAsString()
            ];
        }

        $event->setResponse(new JsonResponse($data, $status));
    }
}
