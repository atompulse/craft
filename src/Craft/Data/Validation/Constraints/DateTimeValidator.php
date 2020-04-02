<?php

namespace Craft\Data\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * \DateTime Validator
 *
 * Validates values which are \DateTimeInterface instance
 * OR which are \DateTimeInterface compatible strings that respect the
 * $constraint->formatStandard||$constraint->formatISO8601UTC formats
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class DateTimeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof DateTime) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\DateTime');
        }

        if ($value instanceof \DateTimeInterface) {
            return;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string or DateTime');
        }

        $value = (string)$value;

        \DateTime::createFromFormat($constraint->formatStandard, $value);
        $errorsStandard = \DateTime::getLastErrors();

        \DateTime::createFromFormat($constraint->formatISO8601UTC, $value);
        $errorsIso = \DateTime::getLastErrors();

        if ($errorsStandard['error_count'] === 0 || $errorsIso['error_count'] === 0) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->setCode(DateTime::INVALID_DATE_TIME_ERROR)
            ->addViolation();
    }
}
