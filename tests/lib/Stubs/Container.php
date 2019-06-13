<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $entries;

    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    public function get($id)
    {
        return $this->entries[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}