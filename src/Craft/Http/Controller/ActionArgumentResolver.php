<?php
declare(strict_types=1);

namespace Craft\Http\Controller;

use Craft\Messaging\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\Security;
use Exception;
use Generator;
use Throwable;

/**
 * Class ActionArgumentResolver
 * @package Craft\Http\Controller
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ActionArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ActionArgumentBuilderInterface
     */
    protected $argumentBuilder;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Security
     */
    private $security;

    /**
     * ActionArgumentResolver constructor.
     * @param LoggerInterface $logger
     * @param Security $security
     * @param ActionArgumentBuilderInterface $argumentBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        Security $security,
        ActionArgumentBuilderInterface $argumentBuilder
    ) {
        $this->logger = $logger;
        $this->security = $security;
        $this->argumentBuilder = $argumentBuilder;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        $argClass = $argument->getType();
        $classExists = class_exists($argClass);

        $classImplementsInterface = in_array(RequestInterface::class, class_implements($argClass));

        return $classExists && $classImplementsInterface;

    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return Generator
     * @throws Throwable
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $argClass = $argument->getType();

        if (!$request->attributes->has('_controller')) {
            throw new Exception("A valid controller is missing from the request");
        }

        // extract params from request
        switch ($request->getMethod()) {
            case 'GET' :
                $params = $request->query->all();
                break;
            default:
            case 'POST' :
                $params = $request->request->all();
                break;
        }

        // resolve files
        if (count($request->files)) {
            $params['files'] = $request->files->all();
        }

        $this->logger->info("Resolving request object [$argClass] for [{$request->attributes->get('_controller')}]", ['params' => $params]);

        try {

            $argRequest = $this->argumentBuilder->build($params, $argClass);

            $this->logger->info("Resolved request object " . get_class($argRequest) . " (" . json_encode($argRequest->toArray()) . ") from input parameters", [$params]);

            yield $argRequest;
        } catch (Throwable $err) {
            $this->logger->info("Failed to instantiate request class [$argClass] ", ['exception' => $err]);

            // stop execution at this point to avoid further expectation errors
            throw $err;
        }
    }
}
