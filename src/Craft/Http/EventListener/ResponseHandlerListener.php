<?php

namespace Craft\Http\EventListener;

use Craft\Messaging\Http\HttpStatusCodes;
use Craft\Messaging\ResponseInterface;
use Craft\Messaging\Service\ServiceStatusCodes;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

/**
 * Class ResponseHandlerListener
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class ResponseHandlerListener
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
     * @param ViewEvent $event
     */
    public function build(ViewEvent $event)
    {
        if ($event->isMasterRequest()) {
            $event->setResponse($this->assemble($event->getControllerResult()));
        }
    }

    /**
     * @param ResponseInterface $response
     * @return Response
     */
    protected function assemble(ResponseInterface $response): Response
    {
        $statusesWithoutErrors = [
            ServiceStatusCodes::OK,
            ServiceStatusCodes::NO_RESULTS
        ];

        $data = [
            'status' => ServiceStatusCodes::UNEXPECTED_ERROR
        ];

        if ($response !== null) {

            $normalizedData = $response->normalizeData();

            ['status' => $status, 'errors' => $errors] = $normalizedData;

            // convert service status to HTTP status
            $data['status'] = HttpStatusCodes::getName(HttpStatusCodes::getCode($status));

            switch ($status) {
                case ServiceStatusCodes::OK :
                    unset($normalizedData['status']);
                    unset($normalizedData['errors']);
                    $data['payload'] = $normalizedData;
                    break;
                case ServiceStatusCodes::NO_RESULTS :
                default:
                    break;
            }

            if ($errors && !in_array($status, $statusesWithoutErrors)) {
                $data['errors'] = $errors;
            }

            if (!$this->isProduction) {
                $data['debug'] = [
                    'responseClass' => get_class($response),
                    'normalizedData' => $normalizedData,
                    'serviceStatus' => $status
                ];
            }
        } else {
            // Store logs
            $this->logger->error("No response returned by controller", [$response]);
        }

        return new JsonResponse($data, HttpStatusCodes::getCode($data['status']));
    }

}
