<?php

declare(strict_types=1);

namespace Hotrush\Spiral\Testing\EntitiesFactory;

use Cycle\ORM\TransactionInterface;
use Faker\Factory;
use Faker\Generator;
use Spiral\Core\Container;

class EntitiesFactory implements EntitiesFactoryInterface
{
    private Container $container;

    private Generator $faker;

    private array $factories = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->faker = Factory::create();
    }

    public function define(string $entity, callable $callable)
    {
        $this->factories[$entity] = $callable;
    }

    public function create(string $entity, $data = null, bool $persist = true): object
    {
        $factory = $this->getFactory($entity);

        $entity = $this->createEntity($entity);

        $this->populateEntity($entity, $factory, $data);

        if ($persist) {
            $this->container
                ->get(TransactionInterface::class)
                ->persist($entity)
                ->run();
        }

        return $entity;
    }

    public function createMany(string $entity, int $number, $data = null, bool $persist = true): array
    {
        $entities = [];

        for ($i = 1; $i <= $number; $i++) {
            $entities[] = $this->create($entity, $data, $persist);
        }

        return $entities;
    }

    private function getFactory(string $entity): callable
    {
        if (!isset($this->factories[$entity])) {
            throw new \InvalidArgumentException(sprintf('Unknown entity: %s', $entity));
        }

        return $this->factories[$entity];
    }

    private function createEntity(string $entity): object
    {
        return new $entity();
    }

    private function populateEntity(object $entity, callable $factory, $data = null): void
    {
        $entityData = $factory($this->faker);

        if (is_array($data)) {
            $entityData = array_merge($entityData, $data);
        }

        foreach ($entityData as $key => $value) {
            $setter = $this->getKeySetter($key);
            if (method_exists($entity, $setter)) {
                $entity->$setter($value);
            }
        }

        if (is_callable($data)) {
            $data($entity, $this->faker);
        }
    }

    private function getKeySetter($key): string
    {
        $chunks = array_map(function ($chunk) {
            return ucfirst($chunk);
        }, explode('_', $key));

        return sprintf('set%s', implode('', $chunks));
    }
}