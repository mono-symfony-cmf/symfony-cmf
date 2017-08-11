<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\CoreBundle\Tests\Unit\Admin\Extension;

use Symfony\Cmf\Bundle\CoreBundle\Admin\Extension\TranslatableExtension;

class TranslatableExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->formMapper = $this->getMockBuilder(
            'Sonata\AdminBundle\Form\FormMapper'
        )->disableOriginalConstructor()->getMock();

        $this->extension = new TranslatableExtension(array('fr', 'en'), 'some_group');
    }

    public function testFormMapper()
    {
        $this->formMapper->expects($this->once())
            ->method('with')
            ->with('some_group')
            ->will($this->returnSelf());
        $this->formMapper->expects($this->exactly(1))
            ->method('add')
            ->will($this->returnSelf());

        $this->extension->configureFormFields($this->formMapper);
    }
}
