<?php

namespace Craft\Data\Validation;

/**
 * StructuredDataValidatorInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface StructuredDataValidatorInterface
{
    /**
     * Validate $data based on the the structure of $structureClass
     * $structureClass MUST implement DataContainerInterface and DataValidatorInterface
     *
     * @param array $data
     * @param string $structureClass
     * @return array A list of properties obtained from the $structureClass::getValidatorConstraints(), with values as array of ContextualError items
     */
    public function validate(array $data, string $structureClass): array;
}
