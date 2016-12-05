<?php

namespace Netgen\ContentBrowser\Tests\Config;

use Netgen\ContentBrowser\Config\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
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
     * @covers \Netgen\ContentBrowser\Config\Configuration::__construct
     * @covers \Netgen\ContentBrowser\Config\Configuration::getItemType
     */
    public function testGetItemType()
    {
        $this->assertEquals('value', $this->config->getItemType());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getSections
     */
    public function testGetSections()
    {
        $this->assertEquals(array(2, 5), $this->config->getSections());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::setSections
     */
    public function testSetSections()
    {
        $this->config->setSections(array(3, 6));
        $this->assertEquals(array(3, 6), $this->config->getSections());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMinSelected
     */
    public function testGetMinSelected()
    {
        $this->assertEquals(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::setMinSelected
     */
    public function testSetMinSelected()
    {
        $this->config->setMinSelected(5);
        $this->assertEquals(5, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelected()
    {
        $this->assertEquals(3, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::setMaxSelected
     */
    public function testSetMaxSelected()
    {
        $this->config->setMaxSelected(3);
        $this->assertEquals(3, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasTree
     */
    public function testHasTree()
    {
        $this->assertTrue($this->config->hasTree());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasSearch
     */
    public function testHasSearch()
    {
        $this->assertTrue($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasPreview
     */
    public function testHasPreview()
    {
        $this->assertTrue($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getTemplate
     */
    public function testGetTemplate()
    {
        $this->assertEquals('template.html.twig', $this->config->getTemplate());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getColumns
     */
    public function testGetColumns()
    {
        $this->assertEquals(array('columns'), $this->config->getColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumns()
    {
        $this->assertEquals(array('column1', 'column2'), $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::setParameter
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameter
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasParameter
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameters
     */
    public function testParameters()
    {
        $this->config->setParameter('param', 'value');
        $this->config->setParameter('param2', 'value2');
        $this->assertEquals('value', $this->config->getParameter('param'));

        $this->assertTrue($this->config->hasParameter('param'));
        $this->assertFalse($this->config->hasParameter('other'));

        $this->assertEquals(
            array(
                'param' => 'value',
                'param2' => 'value2',
            ),
            $this->config->getParameters()
        );
    }
}