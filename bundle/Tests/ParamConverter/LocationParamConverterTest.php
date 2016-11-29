<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ParamConverter\Page;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class LocationParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        $this->paramConverter = new LocationParamConverter($this->itemRepositoryMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApply()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class,
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('itemType', 'value');

        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Location(42)));

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertEquals(new Location(42), $request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithMissingLocationId()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class,
            )
        );

        $request = Request::create('/');
        $request->attributes->set('itemType', 'value');

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithMissingItemType()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class,
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithEmptyOptionalLocationId()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class,
                'isOptional' => true,
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('itemType', 'value');

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     */
    public function testApplyWithEmptyRequiredLocationId()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class,
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('itemType', 'value');

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::supports
     */
    public function testSupports()
    {
        $this->assertTrue($this->paramConverter->supports(new ParamConverter(array('class' => LocationInterface::class))));
        $this->assertFalse($this->paramConverter->supports(new ParamConverter(array('class' => ItemInterface::class))));
    }
}