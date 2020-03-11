<?php

namespace Craft\Data\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * NotEmpty Constraint
 * Behaves exactly like NotBlank EXCEPT false values are NOT considered empty
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class NotEmpty extends Constraint
{
    const IS_EMPTY_ERROR = '4d0bab07-5942-47ad-810b-9533f50e8187';
    protected static $errorNames = [
        self::IS_EMPTY_ERROR => 'IS_EMPTY_ERROR',
    ];
    public $message = 'This value should not be empty.';
    public $allowNull = false;

    public $normalizer;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($this->normalizer !== null && !\is_callable($this->normalizer)) {
            throw new InvalidArgumentException(sprintf('The "normalizer" option must be a valid callable ("%s" given).', \is_object($this->normalizer) ? \get_class($this->normalizer) : \gettype($this->normalizer)));
        }
    }
}
