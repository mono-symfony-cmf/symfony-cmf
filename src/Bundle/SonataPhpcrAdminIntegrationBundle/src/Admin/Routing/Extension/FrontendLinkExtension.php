<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Routing\Extension;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersReadInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingExceptionInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Admin extension to add a frontend link to the edit tab implementing the
 * RouteReferrersReadInterface.
 *
 * @author Frank Neff <fneff89@gmail.com>
 */
class FrontendLinkExtension extends AbstractAdminExtension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function configureSideMenu(
        AdminInterface $admin,
        MenuItemInterface $menu,
        $action,
        AdminInterface $childAdmin = null
    ) {
        $this->configureTabMenu($admin, $menu, $action, $childAdmin);
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function configureTabMenu(
        AdminInterface $admin,
        MenuItemInterface $menu,
        $action,
        AdminInterface $childAdmin = null
    ) {
        if (!$subject = $admin->getSubject()) {
            return;
        }

        if (!$subject instanceof RouteReferrersReadInterface && !$subject instanceof Route) {
            throw new InvalidConfigurationException(
                sprintf(
                    '%s can only be used on subjects which implement Symfony\Cmf\Component\Routing\RouteReferrersReadInterface or Symfony\Component\Routing\Route.',
                    __CLASS__
                )
            );
        }

        if ($subject instanceof PrefixInterface && !is_string($subject->getId())) {
            // we have an unpersisted dynamic route
            return;
        }

        $defaults = array();
        if ($subject instanceof TranslatableInterface) {
            if ($locale = $subject->getLocale()) {
                $defaults['_locale'] = $locale;
            }
        }

        try {
            $uri = $this->router->generate($subject, $defaults);
        } catch (RoutingExceptionInterface $e) {
            // we have no valid route
            return;
        }

        $menu->addChild(
            $this->translator->trans('admin.menu_frontend_link_caption', array(), 'CmfRoutingBundle'),
            array(
                'uri' => $uri,
                'attributes' => array(
                    'class' => 'sonata-admin-menu-item',
                    'role' => 'menuitem',
                ),
                'linkAttributes' => array(
                    'class' => 'sonata-admin-frontend-link',
                    'role' => 'button',
                    'target' => '_blank',
                    'title' => $this->translator->trans('admin.menu_frontend_link_title', array(), 'CmfRoutingBundle'),
                ),
            )
        );
    }
}
