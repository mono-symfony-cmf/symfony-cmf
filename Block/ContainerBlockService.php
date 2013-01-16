<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Cmf\Bundle\BlockBundle\Document\ContainerBlock;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockRendererInterface;

class ContainerBlockService extends BaseBlockService implements BlockServiceInterface
{

    protected $blockRenderer;

    /**
     * @param $name
     * @param \Symfony\Component\Templating\EngineInterface $templating
     */
    public function __construct($name, EngineInterface $templating, BlockRendererInterface $blockRenderer)
    {
        parent::__construct($name, $templating);
        $this->blockRenderer = $blockRenderer;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $form
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        // TODO: Implement buildEditForm() method.
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $form
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function buildCreateForm(FormMapper $form, BlockInterface $block)
    {
        // TODO: Implement buildCreateForm() method.
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return 'SymfonyCmfBlockBundle:Block:block_container.html.twig';
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if ($block->getEnabled()) {
            // merge settings
            $settings = is_array($block->getSettings()) ? array_merge($this->getDefaultSettings(), $block->getSettings()) : $this->getDefaultSettings();

            $childBlocks = array();
            foreach ($block->getChildren()->getValues() as $childBlock) {
                $childBlocks[] =  $this->blockRenderer->render($childBlock)->getContent();
            }

            return $this->renderResponse($this->getTemplate(), array(
                'childBlocks' => $childBlocks,
                'settings'    => $settings
            ), $response);
        }

        return $response;
    }

    /**
     * @param \Sonata\AdminBundle\Validator\ErrorElement $errorElement
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * Returns the default settings link to the service
     *
     * @return array
     */
    public function getDefaultSettings()
    {
        return array(
            'divisibleBy'    => false,
            'divisibleClass' => '',
            'childClass'     => '',
        );
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return void
     */
    public function load(BlockInterface $block)
    {
        // TODO: Implement load() method.
    }

    /**
     * @param $media
     * @return array
     */
    public function getJavascripts($media)
    {
        // TODO: Implement getJavascripts() method.
    }

    /**
     * @param $media
     * @return array
     */
    public function getStylesheets($media)
    {
        // TODO: Implement getStylesheets() method.
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @return array
     */
    public function getCacheKeys(BlockInterface $block)
    {
        // TODO: Implement getCacheKeys() method.
    }
}