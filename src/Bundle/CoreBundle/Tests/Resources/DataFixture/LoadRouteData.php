<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\CoreBundle\Tests\Resources\DataFixture;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;

use Symfony\Cmf\Bundle\CoreBundle\Tests\Resources\Document\Content;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\Document\Generic;

/**
 * Fixtures class for test data.
 */
class LoadRouteData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $root = $manager->find(null, '/');

        $test = new Generic();
        $test->setNodename('test');
        $test->setParent($root);
        $manager->persist($test);

        $content = new Generic();
        $content->setNodename('content');
        $content->setParent($test);
        $manager->persist($content);

        $aContent = new Content();
        $aContent->id = '/test/content/a';
        $manager->persist($aContent);

        $bContent = new Content();
        $bContent->id = '/test/content/b';
        $manager->persist($bContent);

        $cms = new Generic();
        $cms->setNodename('cms');
        $cms->setParent($test);
        $manager->persist($cms);

        $routes = new Generic();
        $routes->setNodename('routes');
        $routes->setParent($cms);
        $manager->persist($routes);

        $aRoute = new Route();
        $aRoute->setName('a');
        $aRoute->setParent($routes);
        $aRoute->setContent($aContent);
        $manager->persist($aRoute);
        $bRoute = new Route();
        $bRoute->setName('b');
        $bRoute->setParent($routes);
        $bRoute->setContent($bContent);
        $manager->persist($bRoute);
        $manager->flush();
    }
}
