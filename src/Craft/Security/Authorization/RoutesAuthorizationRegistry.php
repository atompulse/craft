<?php

namespace Craft\Security\Authorization;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RoutesAuthorizationManager
 * @package Craft\Security\Authorization
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class RoutesAuthorizationRegistry implements RoutesAuthorizationRegistryInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var array
     */
    protected $requirements = [];

    public function __construct(RouterInterface $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;

        $this->initRegistry();
    }

    /**
     * @param string $route
     * @return array
     * @throws Exception
     */
    public function getRequirements(string $route): array
    {
        if (!isset($this->requirements[$route])) {
            $routes = $this->router->getRouteCollection();
            if (!$routes->get($route)) {
                throw new Exception('RoutesAuthorizationRegistry::getRequirements route [' . $route . '] is not defined');
            }
            $this->requirements[$route] = $this->processRoute($route, $routes[$route]);
        }

        return $this->requirements[$route]['operations'];
    }

    protected function initRegistry()
    {
        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            $this->processRoute($routeName, $route);
        }
    }

    /**
     * @param string $routeName
     * @param Route $route
     */
    protected function processRoute(string $routeName, Route $route)
    {
        $rbac = $route->getOption('rbac');

//        $this->logger->debug('RoutesAuthorizationRegistry::processRoute', ['routeName' => $routeName, 'route' => $route, 'rbac' => $rbac]);

        $entry = [
            'route' => $routeName,
            'operations' => []
        ];

        if (!is_null($rbac)) {
            $entry['operations'] = is_array($rbac)
                ? $rbac
                : [$rbac];
        }

        $this->requirements[$routeName] = $entry;
    }

}
