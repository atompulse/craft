<?php
declare(strict_types=1);

namespace Craft\Http\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class JsonPostDataResolverListener
 * @package Craft\Http\EventListener
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class JsonPostDataResolverListener
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->getMethod() === $request::METHOD_POST) {
            // check if we have a json request
            if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
                // get the json request data
                $data = json_decode($request->getContent(), true);
                // map params data to request
                $request->request->replace(is_array($data) ? $data : []);
            }
        }
    }
}
