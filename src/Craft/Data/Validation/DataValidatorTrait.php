<?php

namespace Craft\Data\Validation;

use Craft\Data\Container\DataContainerInterface;
use LogicException;

/**
 * DataValidatorTrait
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
trait DataValidatorTrait
{
    /**
     * Return a list of Symfony Validation Constraints obtained by analyzing the metadata of a DataContainerInterface object.
     * The returned list will be indexed by the properties of the DataContainerInterface.
     * A multilevel array COULD be returned in case the DataContainerInterface metadata properties contain
     * specification of DataContainerInterface types.
     * @return array
     */
    public function getValidatorConstraints(): array
    {
        if ($this instanceof DataContainerInterface) {
            $validatorConstraints = [];

            $metadata = $this->getProperties();

            foreach ($metadata as $propertyName => $constraints) {
                $validatorConstraints[$propertyName] = (new MetadataConstraintsBuilder())->build($propertyName, $constraints);
            }

            return $validatorConstraints;
        }

        throw new LogicException("DataValidatorTrait MUST be attached on DataContainerInterface classes ONLY");
    }

}
