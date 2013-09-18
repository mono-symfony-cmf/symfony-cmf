<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;

class MenuAdmin extends AbstractMenuNodeAdmin
{
    protected $baseRouteName = 'cmf_menu';
    protected $baseRoutePattern = '/cmf/menu/menu';

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $subject = $this->getSubject();
        $isNew = $subject->getId() ? false : true;

        if (false === $isNew) {
            $formMapper
                ->with('form.group_items', array())
                ->add('children', 'doctrine_phpcr_odm_tree_manager', array(
                    'root' => $this->menuRoot,
                    'edit_in_overlay' => false,
                    'create_in_overlay' => false,
                ), array(
                    'help' => 'help.items_help'
                ))
                ->end()
            ;
        }
    }

    public function getNewInstance()
    {
        /** @var $new Menu */
        $new = parent::getNewInstance();
        $new->setParent($this->getModelManager()->find(null, $this->menuRoot));

        return $new;
    }
}