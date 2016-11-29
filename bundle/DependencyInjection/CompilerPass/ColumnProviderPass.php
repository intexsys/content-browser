<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\ContentBrowser\Exceptions\RuntimeException;

class ColumnProviderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_content_browser.column_provider';
    const TAG_NAME = 'netgen_content_browser.column_value_provider';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $columnProvider = $container->findDefinition(self::SERVICE_NAME);
        $valueProviderServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $valueProviders = array();
        foreach ($valueProviderServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new RuntimeException(
                        "Column value provider definition must have a 'identifier' attribute in its' tag."
                    );
                }

                $valueProviders[$tag['identifier']] = new Reference($serviceName);
            }
        }

        $columnProvider->replaceArgument(2, $valueProviders);
    }
}