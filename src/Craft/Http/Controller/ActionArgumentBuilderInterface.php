<?php

namespace Craft\Http\Controller;

use Craft\Messaging\RequestInterface;

/**
 * Interface ActionArgumentBuilderInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface ActionArgumentBuilderInterface
{
    /**
     * @param array $inputData
     * @param string $inputClass
     * @return RequestInterface
     */
    public function build(array $inputData, string $inputClass): RequestInterface;

}
