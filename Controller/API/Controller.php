<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

abstract class Controller extends BaseController
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface
     */
    protected $itemRepository;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface
     */
    protected $itemSerializer;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface $itemSerializer
     * @param array $config
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository,
        ItemSerializerInterface $itemSerializer,
        array $config
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemSerializer = $itemSerializer;
        $this->config = $config;
    }

    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Pagerfanta\Pagerfanta
     */
    protected function buildPager(AdapterInterface $adapter, Request $request)
    {
        $currentPage = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 0);

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);
        $pager->setMaxPerPage(
            $limit > 0 ?
                $limit :
                $this->getParameter('netgen_content_browser.browser.default_limit')
        );

        return $pager;
    }

    /**
     * Builds the path array for specified item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return array
     */
    protected function buildPath(LocationInterface $location)
    {
        $path = array();

        while (true) {
            $path[] = array(
                'id' => $location->getId(),
                'name' => $location->getName(),
            );

            if (in_array($location->getId(), $this->config['sections'])) {
                break;
            }

            if ($location->getParentId() === null) {
                break;
            }

            try {
                $location = $this->itemRepository->loadLocation(
                    $location->getParentId(),
                    $location->getType()
                );
            } catch (NotFoundException $e) {
                break;
            }
        }

        return array_reverse($path);
    }
}
