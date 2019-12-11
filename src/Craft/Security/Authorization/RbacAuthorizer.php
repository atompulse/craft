<?php

namespace Craft\Security\Authorization;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class RbacAuthorizer
 * @package Craft\Security\Authorization
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class RbacAuthorizer implements VoterInterface
{
    protected $security;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var RoutesAuthorizationRegistry
     */
    protected $routesAuthRegistry;

    public function __construct(
        Security $security,
        RoutesAuthorizationRegistry $routesAuthorization,
        LoggerInterface $logger
    ) {
        $this->security = $security;
        $this->logger = $logger;
        $this->routesAuthRegistry = $routesAuthorization;
    }

    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        if ($subject instanceof Request) {
            $requirements = $this->routesAuthRegistry->getRequirements($subject->get('_route'));
            $available = $this->security->getUser()->getRoles();
            $result = array_intersect($requirements, $available);

            $this->logger->info(
                'RbacAuthorizer::vote',
                [
                    'route' => $subject->get('_route'),
                    'required operations' => $requirements,
                    'having operations' => $result,
                ]
            );

            return count($result) == count($requirements) ? VoterInterface::ACCESS_GRANTED : VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
