<?php

namespace Container3bL3Ll2;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_47vzleaService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.47vzlea' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.47vzlea'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'interest' => ['privates', '.errored..service_locator.47vzlea.App\\Entity\\Interest', NULL, 'Cannot autowire service ".service_locator.47vzlea": it needs an instance of "App\\Entity\\Interest" but this type has been excluded in "config/services.yaml".'],
        ], [
            'interest' => 'App\\Entity\\Interest',
        ]);
    }
}
