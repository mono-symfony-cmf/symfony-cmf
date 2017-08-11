<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->addCollection(
    $loader->import(CMF_TEST_CONFIG_DIR.'/routing/sonata_routing.yml')
);
// $collection->addCollection(
//     $loader->import(__DIR__.'/routing/my_test_routing.yml')
// );

return $collection;
