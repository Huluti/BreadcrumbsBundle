<?php

namespace Huluti\BreadcrumbsBundle\Attribute;

use Huluti\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Represents a breadcrumb configuration with various parameters for URL generation, translation, and namespace-based grouping.
 *
 * This attribute can be applied to classes or methods to define breadcrumb metadata.
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Breadcrumb
{
    public function __construct(
        private readonly string $namespace = Breadcrumbs::DEFAULT_NAMESPACE,
        private string $text = '',
        private readonly ?string $url = null,
        private readonly ?string $route = null,
        private array $parameters = [],
        private readonly int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
        private readonly array $translationParameters = [],
        private readonly bool $translate = true
    ) {}

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getReferenceType(): int
    {
        return $this->referenceType;
    }

    public function getTranslationParameters(): array
    {
        return $this->translationParameters;
    }

    public function isTranslate(): bool
    {
        return $this->translate;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
