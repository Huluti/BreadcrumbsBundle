<?php

namespace Huluti\BreadcrumbsBundle\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class BreadcrumbPlaceholderResolver
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Resolves placeholders in the given text using data from the arguments.
     *
     * @param string $text      The text containing placeholders like {object.property}
     * @param array  $arguments The named arguments from the controller
     *
     * @return string The text with resolved placeholders
     */
    public function resolveText(string $text, array $arguments): string
    {
        $pattern = '/\{[^}]+\}/';

        return preg_replace_callback($pattern, function ($matches) use ($arguments) {
            $resolved = $this->resolvePlaceholder($matches[0], $arguments);

            return $resolved ?? $matches[0];
        }, $text);
    }

    /**
     * Resolves a single placeholder value.
     *
     * @param string $placeholder The placeholder like {object.property}
     * @param array  $arguments   The named arguments from the controller
     *
     * @return null|string The resolved value or null if cannot resolve
     */
    public function resolvePlaceholder(string $placeholder, array $arguments): ?string
    {
        $fullPathAttribute = trim($placeholder, '{}');
        if (empty($fullPathAttribute)) {
            return null;
        }

        $parts = explode('.', $fullPathAttribute);
        $objectName = $parts[0];

        if (!isset($arguments[$objectName])) {
            return null;
        }

        $object = $arguments[$objectName];
        $propertyPath = implode('.', array_slice($parts, 1));

        if (empty($propertyPath)) {
            return (string) $object;
        }

        try {
            $value = $this->propertyAccessor->getValue($object, $propertyPath);

            return null !== $value ? (string) $value : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Resolves parameters array containing placeholders.
     *
     * @param array $parameters The parameters array with possible placeholders
     * @param array $arguments  The named arguments from the controller
     *
     * @return array The parameters with resolved placeholders
     */
    public function resolveParameters(array $parameters, array $arguments): array
    {
        $resolved = [];
        foreach ($parameters as $key => $value) {
            if (is_string($value) && str_starts_with($value, '{') && str_ends_with($value, '}')) {
                $resolved[$key] = $this->resolvePlaceholder($value, $arguments) ?? $value;
            } else {
                $resolved[$key] = $value;
            }
        }

        return $resolved;
    }
}
