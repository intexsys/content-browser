<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Config;

use Netgen\Bundle\ContentBrowserBundle\Config\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface
     */
    protected $config;

    public function setUp()
    {
        $configArray = array(
            'sections' => array(2, 5),
            'min_selected' => 1,
            'max_selected' => 3,
            'tree' => array(
                'enabled' => true,
            ),
            'search' => array(
                'enabled' => true,
            ),
            'preview' => array(
                'enabled' => true,
                'template' => 'template.html.twig',
            ),
            'columns' => array('columns'),
            'default_columns' => array('column1', 'column2'),
        );

        $this->config = new Configuration('value', $configArray);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getValueType
     */
    public function testGetValueType()
    {
        self::assertEquals('value', $this->config->getValueType());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getSections
     */
    public function testGetSections()
    {
        self::assertEquals(array(2, 5), $this->config->getSections());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::setSections
     */
    public function testSetSections()
    {
        $this->config->setSections(array(3, 6));
        self::assertEquals(array(3, 6), $this->config->getSections());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getMinSelected
     */
    public function testGetMinSelected()
    {
        self::assertEquals(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::setMinSelected
     */
    public function testSetMinSelected()
    {
        $this->config->setMinSelected(5);
        self::assertEquals(5, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelected()
    {
        self::assertEquals(3, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::setMaxSelected
     */
    public function testSetMaxSelected()
    {
        $this->config->setMaxSelected(3);
        self::assertEquals(3, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::hasTree
     */
    public function testHasTree()
    {
        self::assertTrue($this->config->hasTree());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::hasSearch
     */
    public function testHasSearch()
    {
        self::assertTrue($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::hasPreview
     */
    public function testHasPreview()
    {
        self::assertTrue($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getTemplate
     */
    public function testGetTemplate()
    {
        self::assertEquals('template.html.twig', $this->config->getTemplate());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getColumns
     */
    public function testGetColumns()
    {
        self::assertEquals(array('columns'), $this->config->getColumns());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumns()
    {
        self::assertEquals(array('column1', 'column2'), $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::setParameter
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getParameter
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::hasParameter
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\Configuration::getParameters
     */
    public function testParameters()
    {
        $this->config->setParameter('param', 'value');
        $this->config->setParameter('param2', 'value2');
        self::assertEquals('value', $this->config->getParameter('param'));

        self::assertTrue($this->config->hasParameter('param'));
        self::assertFalse($this->config->hasParameter('other'));

        self::assertEquals(
            array(
                'param' => 'value',
                'param2' => 'value2',
            ),
            $this->config->getParameters()
        );
    }
}