<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Features\Context\ResourceContext;
use Symfony\Cmf\Component\Testing\HttpKernel\TestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This is the kernel used by the application being tested.
 */
class AppKernel extends TestKernel
{
    private $configPath;

    public function setConfig($configPath)
    {
        $this->config = $configPath;
    }

    public function configure()
    {
        $this->requireBundleSets([
            'default', 'phpcr_odm',
        ]);

        $this->addBundles([
            new \Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Resources\TestBundle\TestBundle(),
            new \Symfony\Cmf\Bundle\ResourceRestBundle\CmfResourceRestBundle(),
            new \Symfony\Cmf\Bundle\ResourceBundle\CmfResourceBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
        ]);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.php');

        if ($this->getEnvironment() !== 'behat' && file_exists(ResourceContext::getConfigurationFile())) {
            $loader->import(ResourceContext::getConfigurationFile());
        }
    }
}
