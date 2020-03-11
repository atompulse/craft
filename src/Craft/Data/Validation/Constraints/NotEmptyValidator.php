<?php

namespace Craft\Data\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * NotEmpty Validator
 * Behaves exactly like NotBlank EXCEPT false values are NOT considered empty
 * Bonus: conditions are human readable
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class NotEmptyValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotEmpty) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\NotEmpty');
        }

        if ($constraint->allowNull && $value === null) {
            return;
        }

        if (is_bool($value)) {
            return;
        }

        if (is_string($value) && $constraint->normalizer !== null) {
            $value = ($constraint->normalizer)($value);
        }

        if (empty($value) && $value !== '0' && !is_bool($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(NotEmpty::IS_EMPTY_ERROR)
                ->addViolation();
        }
    }
}
