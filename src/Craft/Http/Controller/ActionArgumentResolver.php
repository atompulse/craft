<?php
declare(strict_types=1);

namespace Craft\Http\Controller;

use Craft\Messaging\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Exception;
use Generator;
use Throwable;

/**
 * Class ActionArgumentResolver
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
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
     * ActionArgumentResolver constructor.
     * @param LoggerInterface $logger
     * @param ActionArgumentBuilderInterface $argumentBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        ActionArgumentBuilderInterface $argumentBuilder
    ) {
        $this->logger = $logger;
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

        // get route params
        $routeParams = $request->attributes->get('_route_params');

        // extract params from query/request
        switch ($request->getMethod()) {
            case 'GET' :
                $params = $request->query->all();
                break;
            case 'POST' :
            default:
                $params = $request->request->all();
                break;
        }

        $params = array_merge($params, $routeParams);

        // resolve files
        if (count($request->files)) {
            $params['files'] = $request->files->all();
        }

        $this->logger->info("Resolving request object [$argClass] for [{$request->attributes->get('_controller')}]", ['params' => $params]);

        try {

            $argRequest = $this->argumentBuilder->build($params, $argClass);

            if (!array_key_exists('files', $params)) {
                $this->logger->info("Resolved request object " . get_class($argRequest) . " (" . json_encode($argRequest->normalizeData()) . ") from input parameters", [$params]);
            } else {
                $this->logger->info("Resolved request object " . get_class($argRequest) . " from input parameters", [$params]);
            }

            yield $argRequest;
        } catch (Throwable $err) {
            $this->logger->info("Failed to instantiate request class [$argClass] ", ['exception' => $err]);

            // stop execution at this point to avoid further expectation errors
            throw $err;
        }
    }
}
