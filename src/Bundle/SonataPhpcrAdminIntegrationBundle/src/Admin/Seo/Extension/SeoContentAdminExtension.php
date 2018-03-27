<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Seo\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;
use Symfony\Cmf\Bundle\SeoBundle\Form\Type\SeoMetadataType;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;

/**
 * This AbstractAdminExtension will serve the bundle's own form type
 * for configuring seo metadata.
 *
 * @author Maximilian Berghoff <maximilian.berghoff@gmx.de>
 */
class SeoContentAdminExtension extends AbstractAdminExtension
{
    /**
     * @var string
     */
    protected $formGroup;

    protected $formTab;

    /**
     * @param string $formGroup group name to use for form mapper
     * @param string $formTab
     */
    public function __construct($formGroup = 'form.group_seo', $formTab = 'form.tab_seo')
    {
        $this->formGroup = $formGroup;
        $this->formTab = $formTab;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        if ($formMapper->hasOpenTab()) {
            $formMapper->end();
        }

        $formMapper
            ->tab($this->formTab, 'form.tab_seo' === $this->formTab
                ? ['translation_domain' => 'CmfSonataPhpcrAdminIntegrationBundle']
                : []
            )
                ->with($this->formGroup, 'form.group_seo' === $this->formGroup
                    ? ['translation_domain' => 'CmfSonataPhpcrAdminIntegrationBundle']
                    : []
                )
                    ->add('seoMetadata', SeoMetadataType::class, [
                        'label' => false,
                        'label_format' => 'form.label_%name%',
                        'translation_domain' => 'CmfSonataPhpcrAdminIntegrationBundle',
                    ])
                ->end()
            ->end()
        ;
    }

    public function preUpdate(AdminInterface $admin, $seoAware)
    {
        $this->propagateLocale($seoAware);
    }

    public function prePersist(AdminInterface $admin, $seoAware)
    {
        $this->propagateLocale($seoAware);
    }

    /**
     * The seo metadata that was edited embedded has the same locale as the
     * containing document.
     *
     * @param SeoAwareInterface $seoAware
     */
    private function propagateLocale(SeoAwareInterface $seoAware)
    {
        if (!$seoAware instanceof TranslatableInterface) {
            return;
        }

        $seoMetadata = $seoAware->getSeoMetadata();

        if (!$seoMetadata instanceof TranslatableInterface) {
            return;
        }

        $seoMetadata->setLocale($seoAware->getLocale());
    }
}
