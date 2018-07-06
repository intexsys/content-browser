<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Tests\Kernel\MockerContainer;
use Symfony\Component\HttpFoundation\Response;

final class RenderItemTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\RenderItem::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\RenderItem::__invoke
     */
    public function testRenderItem(): void
    {
        $container = $this->client->getContainer();
        if (!$container instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->identicalTo(42))
            ->will($this->returnValue(new Item(42, 'Item 42')));

        $container->set(
            'netgen_content_browser.config.test',
            new Configuration(
                'test',
                [
                    'columns' => [
                        'name' => [
                            'name' => 'columns.name',
                            'value_provider' => 'name',
                        ],
                    ],
                    'default_columns' => ['name'],
                    'preview' => [
                        'enabled' => true,
                        'template' => 'template.html.twig',
                    ],
                ]
            )
        );

        $this->client->request('GET', '/cb/api/v1/test/render/42');

        $response = $this->client->getResponse();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseCode($response, Response::HTTP_OK);
        $this->assertContains('text/html', $response->headers->get('Content-Type'));
        $this->assertSame('rendered item', $response->getContent());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\RenderItem::__invoke
     */
    public function testRenderItemWithDisabledPreview(): void
    {
        $container = $this->client->getContainer();
        if (!$container instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->identicalTo(42))
            ->will($this->returnValue(new Item(42, 'Item 42')));

        $container->set(
            'netgen_content_browser.config.test',
            new Configuration(
                'test',
                [
                    'columns' => [
                        'name' => [
                            'name' => 'columns.name',
                            'value_provider' => 'name',
                        ],
                    ],
                    'default_columns' => ['name'],
                    'preview' => [
                        'enabled' => false,
                    ],
                ]
            )
        );

        $this->client->request('GET', '/cb/api/v1/test/render/42');

        $response = $this->client->getResponse();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseCode($response, Response::HTTP_OK);
        $this->assertContains('text/html', $response->headers->get('Content-Type'));
        $this->assertSame('', $response->getContent());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\RenderItem::__invoke
     */
    public function testRenderItemWithNonExistingItem(): void
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->identicalTo(42))
            ->will($this->throwException(new NotFoundException('Item does not exist.')));

        $this->client->request('GET', '/cb/api/v1/test/render/42');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Item does not exist.'
        );
    }
}
