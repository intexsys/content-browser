<?php

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBrowserDynamicTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        return new ContentBrowserDynamicType(
            $this->itemRepositoryMock,
            array('value1' => 'Value 1', 'value2' => 'Value 2')
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildForm
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getEnabledItemTypes
     */
    public function testSubmitValidDataWithNoItemTypeLimit()
    {
        $form = $this->factory->create(
            ContentBrowserDynamicType::class
        );

        $data = array('item_id' => '42', 'item_type' => 'value2');

        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildForm
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getEnabledItemTypes
     */
    public function testSubmitValidDataWithItemTypeLimit()
    {
        $form = $this->factory->create(
            ContentBrowserDynamicType::class,
            null,
            array(
                'item_types' => array('value1'),
            )
        );

        $data = array('item_id' => '42', 'item_type' => 'value1');

        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildView()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42), $this->equalTo('value1'))
            ->will($this->returnValue(new Item(42)));

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $data = array('item_id' => 42, 'item_type' => 'value1');

        $form->submit($data);

        $view = $form->createView();

        $this->assertArrayHasKey('item_name', $view->vars);
        $this->assertEquals('This is a name (42)', $view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildViewWithNonExistingItem()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42), $this->equalTo('value1'))
            ->will($this->throwException(new NotFoundException()));

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $data = array('item_id' => 42, 'item_type' => 'value1');

        $form->submit($data);

        $view = $form->createView();

        $this->assertArrayHasKey('item_name', $view->vars);
        $this->assertNull($view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildViewWithEmptyData()
    {
        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadItem');

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $form->submit(null);

        $view = $form->createView();

        $this->assertArrayHasKey('item_name', $view->vars);
        $this->assertNull($view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'item_types' => array('value1'),
            )
        );

        $this->assertEquals($options['item_types'], array('value1'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     */
    public function testConfigureOptionsWithMissingItemTypes()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $this->assertEquals($options['item_types'], array());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidItemTypes()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('item_types' => 42));
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidItemTypesItem()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('item_types' => array(42)));
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ng_content_browser_dynamic', $this->formType->getBlockPrefix());
    }
}