<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\RoutingAuto\Tests\Unit\TokenProvider;

use Symfony\Cmf\Component\RoutingAuto\TokenProvider\SymfonyContainerParameterProvider;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SymfonyContainerParameterProviderTest extends \PHPUnit_Framework_TestCase
{
    private $uriContext;
    private $container;

    public function setUp()
    {
        $this->uriContext = $this->prophesize('Symfony\Cmf\Component\RoutingAuto\UriContext');
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->provider = new SymfonyContainerParameterProvider($this->container->reveal());
    }

    public function provideParameterValue()
    {
        return [
            [['parameter' => 'foobar'], null],
            [['foobar' => 'barfoo'], 'InvalidArgumentException'], // This is deliberately generic to preserve BC from SF 2.5 > 2.6
            [[], 'Symfony\Component\OptionsResolver\Exception\MissingOptionsException'],
        ];
    }

    /**
     * @dataProvider provideParameterValue
     */
    public function testParameterValue($options, $expectedException)
    {
        if (null !== $expectedException) {
            $this->setExpectedException($expectedException);
        }

        $this->container->getParameter('foobar')->willReturn('barfoo');

        $optionsResolver = new OptionsResolver();
        $this->provider->configureOptions($optionsResolver);
        $options = $optionsResolver->resolve($options);

        $res = $this->provider->provideValue($this->uriContext->reveal(), $options);

        $this->assertEquals('barfoo', $res);
    }
}
