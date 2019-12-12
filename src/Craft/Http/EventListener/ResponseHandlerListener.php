<?php

namespace Craft\Http\EventListener;

use Atompulse\Component\Domain\Data\DataContainerInterface;
use Craft\Messaging\Http\HttpStatusCodes;
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
     * @param DataContainerInterface $dto
     * @return Response
     */
    protected function assemble(DataContainerInterface $dto): Response
    {
        $data = [];

        if ($dto !== null) {
            $normalizedData = $dto->normalizeData();
            [$status, $errors] = $normalizedData;
            unset($normalizedData['status']);
            unset($normalizedData['errors']);
            $data['payload'] = $normalizedData;
            $data['status'] = $status;

            if ($data['status'] !== ServiceStatusCodes::OK) {
                $data['errors'] = $errors;
            }
        }

        return new JsonResponse($data, HttpStatusCodes::getCode($status));
    }

}
