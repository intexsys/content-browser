<?php

namespace Netgen\ContentBrowser\Item\Renderer;

use Exception;
use Netgen\ContentBrowser\Item\ItemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;

final class ItemRenderer implements ItemRendererInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(Environment $twig, LoggerInterface $logger = null)
    {
        $this->twig = $twig;
        $this->logger = $logger ?: new NullLogger();
    }

    public function renderItem(ItemInterface $item, $template)
    {
        try {
            return $this->twig->render(
                $template,
                array(
                    'item' => $item,
                )
            );
        } catch (Exception $e) {
            $this->logger->error(
                sprintf(
                    'An error occurred while rendering an item with "%s" value: %s',
                    $item->getValue(),
                    $e->getMessage()
                )
            );

            return '';
        }
    }
}
