<?php

namespace Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class Factory
{
    protected $mapping;

    // we lazy-load the builder chains, this will allow us to support
    // addition annotation/mapping in the future.
    protected $routeStackChains;

    protected $serviceIds = array(
        'provider' => array(),
        'exists_action' => array(),
        'not_exists_action' => array(),
    );

    protected $container;
    protected $builder;

    public function __construct(ContainerInterface $container, RouteStackBuilder $builder)
    {
        $this->container = $container;
        $this->builder = $builder;
    }

    public function registerMapping($classFqn, $config)
    {
        $this->mapping[$classFqn] = $config;
    }

    public function registerAlias($type, $alias, $id)
    {
        if (!isset($this->serviceIds[$type])) {
            throw new \RuntimeException(sprintf('Unknown service ID type "%s"', $type));
        }

        $this->serviceIds[$type][$alias] = $id;
    }

    public function getRouteStackBuilderChain($classFqn)
    {
        if (!isset($this->routeStackChains[$classFqn])) {
            $this->routeStackChains[$classFqn] = $this->generateRouteStackChain($classFqn);
        }

        return $this->routeStackChains[$classFqn];
    }

    public function getContentRouteBuilder($classFqn)
    {
        $mapping = $this->getMapping($classFqn);
        return $this->generateBuilderUnit($mapping['content_name']);
    }

    public function hasMapping($classFqn)
    {
        // @todo: Do we need to support inheritance?
        return isset($this->mapping[$classFqn]);
    }

    protected function generateRouteStackChain($classFqn)
    {
        $mapping = $this->getMapping($classFqn);

        $routeStackChain = new RouteStackBuilderUnitChain($this->builder);

        foreach ($mapping['content_path'] as $builderName => $builderConfig) {
            $builderUnit = $this->generateBuilderUnit($builderConfig);
            $routeStackChain->addRouteStackBuilderUnit($builderName, $builderUnit);
        }

        return $routeStackChain;
    }

    protected function generateBuilderUnit($config)
    {
        $pathProvider = $this->getBuilderService($builderConfig, 'provider', 'name');
        $existsAction = $this->getBuilderService($builderConfig, 'exists_action', 'strategy');
        $notExistsAction = $this->getBuilderService($builderConfig, 'not_exists_action', 'strategy');

        $builderUnit = new RouteStackBuilderUnit(
            $pathProvider,
            $existsAction,
            $notExistsAction
        );

        return $builderUnit;
    }

    protected function getMapping($classFqn)
    {
        if (!isset($this->mapping[$classFqn])) {
            throw new Exception\ClassNotMappedException($classFqn);
        }

        $mapping = $this->mapping[$classFqn];
        $this->validateMapping($classFqn, $mapping);

        return $mapping;
    }

    private function validateMapping($classFqn, $mapping)
    {
        $exists = function ($name, $check) use ($classFqn, $mapping) {
            if (!$check($mapping)) {
                throw new \RuntimeException(sprintf(
                    '%s not defined in mapping for class "%s": %s', 
                    $name, 
                    $classFqn,
                    print_r($mapping, true)
                ));
            }
        };

        $exists('content_path', function ($mapping) { return isset($mapping['content_path']); });
        $exists('content_name', function ($mapping) { return isset($mapping['content_name']); });
    }

    private function getBuilderService($builderConfig, $type, $aliasKey)
    {
        if (!isset($builderConfig[$type])) {
            throw new \RuntimeException(sprintf('Builder config has not defined "%s": %s', 
                $type, 
                print_r($builderConfig, true)
            ));
        }

        if (!isset($builderConfig[$type][$aliasKey])) {
            throw new \RuntimeException(sprintf('Builder config has not alias key "%s" for "%s": %s', 
                $aliasKey, 
                $type,
                print_r($builderConfig[$type], true)
            ));
        }

        $alias = $builderConfig[$type][$aliasKey];

        if (!isset($this->serviceIds[$type][$alias])) {
            throw new \RuntimeException(sprintf('"%s" class with alias "%s" requested, but this alias does not exist.',
                $type,
                $alias
            ));
        }

        $serviceId = $this->serviceIds[$type][$alias];

        // NOTE: Services must always be defined as scope=prototype for them
        //       to be stateless (which is good here)
        $service = $this->container->get($serviceId);
        unset($builderConfig[$type][$aliasKey]);
        $service->init($builderConfig[$type]);

        return $service;
    }
}
