<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Huluti\BreadcrumbsBundle\Model\Breadcrumbs;
use Huluti\BreadcrumbsBundle\Templating\Helper\BreadcrumbsHelper;
use Huluti\BreadcrumbsBundle\Twig\Extension\BreadcrumbsExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Our service, for controllers
    $services->set(Breadcrumbs::class)
        ->call('setRouter', [service('router')])
        ->tag('kernel.reset', ['method' => 'clear'])
    ;

    $services->alias('huluti_breadcrumbs', Breadcrumbs::class)
        ->public()
    ;

    // Templating helper
    $services->set('huluti_breadcrumbs.helper', BreadcrumbsHelper::class)
        ->public()
        ->args([
            service('twig'),
            service('huluti_breadcrumbs'),
            param('huluti_breadcrumbs.options'),
        ])
    ;

    // Twig extension
    $services->set('huluti_breadcrumbs.twig', BreadcrumbsExtension::class)
        ->public()
        ->args([service('service_container')])
        ->tag('twig.extension')
    ;
};
