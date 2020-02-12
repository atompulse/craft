<?php

namespace Craft\Http\EventListener;

use Craft\Http\Controller\Exception\ActionArgumentException;
use Craft\Messaging\Http\HttpStatusCodes;
use Craft\Security\Authentication\Exception\TokenAuthenticatorException;
use Craft\Security\Authentication\Exception\TokenParserException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ExceptionHandlerListener
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
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
        $exception = $event->getThrowable();

        $class = get_class($exception);

        $data = [];

        switch ($class) {
            case TokenAuthenticatorException::class :
            case TokenParserException::class :
                $status = HttpStatusCodes::AUTHENTICATION_ERROR;
                $data['errors'] = $exception->getErrors();
                $this->logger->error(HttpStatusCodes::getName($status) . " error occurred", [$exception]);
                break;
            case AccessDeniedException::class :
                $status = HttpStatusCodes::AUTHORIZATION_ERROR;
                $this->logger->error(HttpStatusCodes::getName($status) . " error occurred", [$exception]);
                break;
            case ActionArgumentException::class :
                $status = HttpStatusCodes::INVALID_INPUT_ERROR;
                $data['errors'] = $exception->getErrors();
                $this->logger->error(HttpStatusCodes::getName($status) . " error occurred", [$data['errors']]);
                break;
            case NotFoundHttpException::class :
            case MethodNotAllowedHttpException::class :
                $status = HttpStatusCodes::INVALID_RESOURCE_ERROR;
                $this->logger->error(HttpStatusCodes::getName($status) . " error occurred", [$exception]);
                break;
            default:
                $status = HttpStatusCodes::UNEXPECTED_ERROR;
                $this->logger->error("Unexpected error occurred", [$exception]);
                break;
        }

        $data['status'] = HttpStatusCodes::getName($status);

        if (!$this->isProduction) {
            $data['debug'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        $event->setResponse(new JsonResponse($data, $status));
    }
}
