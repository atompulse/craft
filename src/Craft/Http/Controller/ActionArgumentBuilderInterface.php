<?php

namespace Craft\Http\Controller;

/**
 * Interface ActionArgumentBuilderInterface
 * @package Craft\Http\Controller
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface ActionArgumentBuilderInterface
{

    public function build(array $inputData, string $inputClass): ActionArgumentRequestInterface;

}
