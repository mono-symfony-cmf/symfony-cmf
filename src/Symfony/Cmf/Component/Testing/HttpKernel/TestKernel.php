<?php

namespace Symfony\Cmf\Component\Testing\HttpKernel;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * TestKernel base class for Symfony CMF Bundle
 * integration tests.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
abstract class TestKernel extends Kernel
{
    protected $bundleSets = array();
    protected $requiredBundles = array();

    /**
     * Register commonly needed bundle sets and then
     * after initializing the parent kernel, let the
     * concrete kernel configure itself using the abstracvt
     * configure() command.
     */
    public function init()
    {
        $this->registerBundleSet('default', array(
            '\Symfony\Bundle\FrameworkBundle\FrameworkBundle',
            '\Symfony\Bundle\SecurityBundle\SecurityBundle',
            '\Symfony\Bundle\TwigBundle\TwigBundle',
        ));

        $this->registerBundleSet('phpcr_odm', array(
            '\Doctrine\Bundle\DoctrineBundle\DoctrineBundle',
            '\Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle',
        ));

        $this->registerBundleSet('sonata_admin', array(
            '\Sonata\BlockBundle\SonataBlockBundle',
            '\Sonata\AdminBundle\SonataAdminBundle',
            '\Sonata\DoctrinePHPCRAdminBundle\SonataDoctrinePHPCRAdminBundle',
        ));

        parent::init();
        $this->configure();
    }

    /**
     * Use this method to declare which bundles are required
     * by the Kernel, e.g.
     *
     *    $this->requireBundleSets('default', 'phpcr_odm');
     *    $this->addBundle(new MyBundle);
     *    $this->addBundles(array(new Bundle1, new Bundle2));
     *
     */
    abstract protected function configure();

    /**
     * Register a set of bundles with the given name
     *
     * This method does not add the bundles to the kernel,
     * it just makes a set available.
     */
    public function registerBundleSet($name, $bundles)
    {
        $this->bundleSets[$name] = $bundles;
    }

    /**
     * The bundles in the named sets will be added to the Kernel.
     */
    public function requireBundleSets(array $names)
    {
        foreach ($names as $name) {
            $this->requireBundleSet($name);
        }
    }

    /**
     * Require the bundles in the named bundle set.
     *
     * Note that we register the FQN's and not the concrete classes.
     * This enables us to declare pre-defined bundle sets without
     * worrying if the bundle is actually present or not.
     */
    public function requireBundleSet($name)
    {
        if (!isset($this->bundleSets[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Bundle set %s has not been registered, available bundle sets: %s',
                $name,
                implode(',', array_keys($this->bundleSets))
            ));
        }

        foreach ($this->bundleSets[$name] as $bundle) {
            if (!class_exists($bundle)) {
                throw new \InvalidArgumentException(sprintf(
                    'Bundle class "%s" does not exist.',
                    $bundle
                ));
            }

            $this->requiredBundles[] = new $bundle;
        }
    }

    /**
     * Add concrete bundles to the kernel
     */
    public function addBundles(array $bundles)
    {
        foreach ($bundles as $bundle) {
            $this->addBundle($bundle);
        }
    }

    /**
     * Add a concrete bundle to the kernel
     */
    public function addBundle(BundleInterface $bundle)
    {
        $this->requiredBundles[] = $bundle;
    }

    /**
     * {inheritDoc}
     *
     * Here we return our list of bundles
     */
    public function registerBundles()
    {
        return $this->requiredBundles;
    }

    /**
     * Returns the KernelDir of the CHILD class, 
     * i.e. the concrete implementation in the bundles
     * src/ directory (or wherever).
     */
    public function getKernelDir()
    {
        $refl = new \ReflectionClass($this);
        $fname = $refl->getFileName();
        $kernelDir = dirname($fname);
        return $kernelDir;
    }

    public function getCacheDir()
    {
        return implode('/', array(
            $this->getKernelDir(),
            'cache'
        ));
    }
}