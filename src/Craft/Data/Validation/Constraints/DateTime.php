<?php

namespace Craft\Data\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * \DateTime constraint
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class DateTime extends Constraint
{
    const INVALID_DATE_TIME_ERROR = '2c96242f-9311-4f04-ab12-ed3e6d5e80c3';

    protected static $errorNames = [
        self::INVALID_DATE_TIME_ERROR => 'INVALID_DATE_TIME_ERROR',
    ];

    public $message = 'This value is not a valid DateTime';

    public $formatStandard = 'Y-m-d H:i:s';
    public $formatISO8601UTC = "Y-m-d\TH:i:s.u\Z";

}
