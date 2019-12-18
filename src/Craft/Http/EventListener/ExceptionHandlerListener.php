<?php

namespace Craft\Http\EventListener;

use Craft\Http\Controller\Exception\ActionArgumentException;
use Craft\Messaging\Http\HttpStatusCodes;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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

        $class = get_class($error);

        $data = [];

        switch ($class) {
            case AccessDeniedException::class :
                $status = HttpStatusCodes::AUTHORIZATION_ERROR;
                break;
            case ActionArgumentException::class :
                $status = HttpStatusCodes::INVALID_INPUT_ERROR;
                $data['errors'] = $error->getArgumentsErrors();
                break;
            case NotFoundHttpException::class :
            case MethodNotAllowedHttpException::class :
                $status = HttpStatusCodes::INVALID_RESOURCE_ERROR;
                break;
            default:
                $status = HttpStatusCodes::UNEXPECTED_ERROR;
                break;
        }

        $data['status'] = HttpStatusCodes::getName($status);

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
