<?php

namespace Evosearchers;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Application
 *
 * @package Evosearchers
 */
class Application extends BaseApplication
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Application constructor.
     *
     * @param ContainerBuilder $containerBuilder
     * @param string           $name
     * @param string           $version
     */
    public function __construct(
        ContainerBuilder $containerBuilder,
        string $name = 'UNKNOWN',
        string $version = 'UNKNOWN'
    ) {
        parent::__construct($name, $version);
        $this->container = $containerBuilder;
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }
}
