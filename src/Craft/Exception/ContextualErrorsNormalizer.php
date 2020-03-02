<?php

namespace Craft\Exception;

/**
 * Class ContextualErrorsNormalizer
 *
 * Given a list of ContextualErrorInterface errors,
 * will normalize the errors to a simple list of primitive data [{message, error}, ...]
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class ContextualErrorsNormalizer
{
    /**
     * @param array $contextualErrors
     * @return array
     */
    public function __invoke(array $contextualErrors): array
    {
        $errors = [];
        foreach ($contextualErrors as $error) {
            $errors[] = $error->normalizeData();
        }

        return $errors;
    }
}
