<?php

namespace Huluti\BreadcrumbsBundle\EventListener;

use Huluti\BreadcrumbsBundle\Attribute\Breadcrumb;
use Huluti\BreadcrumbsBundle\Model\Breadcrumbs;
use Huluti\BreadcrumbsBundle\Service\BreadcrumbPlaceholderResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

readonly class BreadcrumbEventSubscriber implements EventSubscriberInterface
{
    private BreadcrumbPlaceholderResolver $placeholderResolver;

    public function __construct(
        private Breadcrumbs $breadcrumbs,
        private RouterInterface $router,
    ) {
        $this->placeholderResolver = new BreadcrumbPlaceholderResolver();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => ['onKernelController', -1],
        ];
    }

    /**
     * Handles the kernel controller event and processes breadcrumb attributes.
     */
    public function onKernelController(ControllerArgumentsEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $arguments = $event->getNamedArguments();

        /** @var Breadcrumb $breadcrumbAttribute */
        foreach ($event->getAttributes(Breadcrumb::class) as $breadcrumbAttribute) {
            $this->processBreadcrumbAttribute($breadcrumbAttribute, $arguments);
        }
    }

    /**
     * Processes a single breadcrumb attribute.
     */
    private function processBreadcrumbAttribute(Breadcrumb $attribute, array $arguments): void
    {
        // Clone attribute to avoid modifying the original
        $processedAttribute = clone $attribute;

        // Process text placeholders
        $text = $processedAttribute->getText();
        if (!empty($text)) {
            $text = $this->placeholderResolver->resolveText($text, $arguments);
            $processedAttribute->setText($text);
        }

        // Process parameters placeholders
        $parameters = $processedAttribute->getParameters();
        if (!empty($parameters)) {
            $resolvedParameters = $this->placeholderResolver->resolveParameters($parameters, $arguments);
            $processedAttribute->setParameters($resolvedParameters);
        }

        $this->addBreadcrumb($processedAttribute);
    }

    /**
     * Adds a breadcrumb to the current namespace.
     */
    private function addBreadcrumb(Breadcrumb $attribute): void
    {
        $url = $attribute->getUrl();
        if (empty($url) && !empty($attribute->getRoute())) {
            $url = $this->router->generate(
                $attribute->getRoute(),
                $attribute->getParameters(),
                $attribute->getReferenceType()
            );
        }

        $this->breadcrumbs->addNamespaceItem(
            $attribute->getNamespace(),
            $attribute->getText(),
            $url,
            $attribute->getTranslationParameters(),
            $attribute->isTranslate()
        );
    }
}
