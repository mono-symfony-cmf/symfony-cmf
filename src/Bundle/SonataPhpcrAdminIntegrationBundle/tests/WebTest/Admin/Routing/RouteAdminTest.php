<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Tests\WebTest\Admin\Routing;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Component\DomCrawler\Crawler;

class RouteAdminTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures([
            'Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Tests\Fixtures\App\DataFixtures\Phpcr\LoadRouteData',
        ]);
        $this->client = $this->createClient();
    }

    public function testRouteList()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/routing/route/list');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);
        $this->assertCount(1, $crawler->filter('html:contains("route-1")'));
    }

    public function testRouteEdit()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/routing/route/test/routing/route-1/edit');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);
        $this->assertCount(1, $crawler->filter('input[value="route-1"]'));

        $this->assertFrontendLinkPresent($crawler);
    }

    public function testRouteShow()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/routing/route/test/routing/route-1/show');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);
    }

    public function testRouteCreate()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/routing/route/create');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);

        $this->assertFrontendLinkNotPresent($crawler);

        $button = $crawler->selectButton('Create');
        $form = $button->form();
        $node = $form->getFormNode();
        $actionUrl = $node->getAttribute('action');
        $uniqId = substr(strstr($actionUrl, '='), 1);

        $form[$uniqId.'[parentDocument]'] = '/test/routing';
        $form[$uniqId.'[name]'] = 'foo-test';

        $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode());
    }

    /**
     * @param Crawler $crawler
     */
    private function assertFrontendLinkPresent(Crawler $crawler)
    {
        $this->assertCount(1, $link = $crawler->filter('a[class="sonata-admin-frontend-link"]'));
        $this->assertEquals('/route-1', $link->attr('href'));
    }

    /**
     * @param Crawler $crawler
     */
    private function assertFrontendLinkNotPresent(Crawler $crawler)
    {
        $this->assertCount(0, $crawler->filter('a[class="sonata-admin-frontend-link"]'));
    }
}
