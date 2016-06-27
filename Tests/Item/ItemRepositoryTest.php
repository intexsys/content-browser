<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository;
use Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Location;
use PHPUnit\Framework\TestCase;

class ItemRepositoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository
     */
    protected $itemRepository;

    public function setUp()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry();
        $backendRegistry->addBackend('value', $this->backendMock);

        $this->itemRepository = new ItemRepository($backendRegistry);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::getDefaultSections
     */
    public function testGetDefaultSections()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getDefaultSections')
            ->will($this->returnValue(array(new Location(42))));

        $sections = $this->itemRepository->getDefaultSections('value');

        self::assertEquals(array(new Location(42)), $sections);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::loadLocation
     */
    public function testLoadLocation()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Location(42)));

        $location = $this->itemRepository->loadLocation(42, 'value');

        self::assertEquals(new Location(42), $location);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::loadItem
     */
    public function testLoadItem()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item(42)));

        $item = $this->itemRepository->loadItem(42, 'value');

        self::assertEquals(new Item(42), $item);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::getSubLocations
     */
    public function testGetSubLocations()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubLocations')
            ->with($this->equalTo(new Location(24)))
            ->will($this->returnValue(array(new Location(42))));

        $locations = $this->itemRepository->getSubLocations(new Location(24));

        self::assertEquals(array(new Location(42)), $locations);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::getSubLocationsCount
     */
    public function testGetSubLocationsCount()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubLocationsCount')
            ->with($this->equalTo(new Location(24)))
            ->will($this->returnValue(2));

        $count = $this->itemRepository->getSubLocationsCount(new Location(24));

        self::assertEquals(2, $count);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::getSubItems
     */
    public function testGetSubItems()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItems')
            ->with(
                $this->equalTo(new Location(24)),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array(new Item(42))));

        $items = $this->itemRepository->getSubItems(new Location(24), 5, 10);

        self::assertEquals(array(new Item(42)), $items);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::getSubItemsCount
     */
    public function testGetSubItemsCount()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('getSubItemsCount')
            ->with($this->equalTo(new Location(24)))
            ->will($this->returnValue(3));

        $count = $this->itemRepository->getSubItemsCount(new Location(24));

        self::assertEquals(3, $count);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::search
     */
    public function testSearch()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('search')
            ->with(
                $this->equalTo('test'),
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->will($this->returnValue(array(new Item(42))));

        $items = $this->itemRepository->search('test', 'value', 5, 10);

        self::assertEquals(array(new Item(42)), $items);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepository::searchCount
     */
    public function testSearchCount()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('searchCount')
            ->with($this->equalTo('test'))
            ->will($this->returnValue(3));

        $count = $this->itemRepository->searchCount('test', 'value');

        self::assertEquals(3, $count);
    }
}
