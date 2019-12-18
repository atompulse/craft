<?php

namespace Craft\Http\EventListener;

use Craft\Messaging\Http\HttpStatusCodes;
use Craft\Messaging\ResponseInterface;
use Craft\Messaging\Service\ServiceStatusCodes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

/**
 * Class ResponseHandlerListener
 * @package Craft\Http\EventListener
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ResponseHandlerListener
{
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
     * @param ResponseInterface $dto
     * @return Response
     */
    protected function assemble(ResponseInterface $dto): Response
    {

        $data = [
            'status' => ServiceStatusCodes::UNEXPECTED_ERROR
        ];

        if ($dto !== null) {

            $normalizedData = $dto->normalizeData();

            ['status' => $status, 'errors' => $errors] = $normalizedData;

            $data['status'] = $status;


//            var_dump($dto);die;

            switch ($status) {
                case ServiceStatusCodes::OK :
                    unset($normalizedData['status']);
                    unset($normalizedData['errors']);
                    $data['payload'] = $normalizedData;
                    break;
                case ServiceStatusCodes::NO_RESULTS :
                    break;
                default:
                    break;
            }

            if ($errors && $status !== ServiceStatusCodes::OK && $status !==ServiceStatusCodes::NO_RESULTS) {
                $data['errors'] = $errors;
            }

//            if (!$this->isProduction) {
//                $data['debug'] = [
//                    'message' => $error->getMessage(),
//                    'file' => $error->getFile(),
//                    'trace' => $error->getTraceAsString()
//                ];
//            }
        }

        return new JsonResponse($data, HttpStatusCodes::getCode($data['status']));
    }

}
