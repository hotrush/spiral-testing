<?php

declare(strict_types=1);

namespace Hotrush\Spiral\Testing\EntitiesFactory;

interface EntitiesFactoryInterface
{
    public function define(string $entity, callable $callable);

    public function create(string $entity, $data = null, bool $persist = true);

    public function createMany(string $entity, int $number, $data = null, bool $persist = true): array;
}