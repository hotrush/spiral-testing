<?php

declare(strict_types=1);

namespace Hotrush\Spiral\Testing\Traits;

use Hotrush\Spiral\Testing\Constraints\CountInDatabase;
use Hotrush\Spiral\Testing\Constraints\HasInDatabase;
use Hotrush\Spiral\Testing\EntitiesFactory\EntitiesFactoryInterface;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\DatabaseManager;
use PHPUnit\Framework\Constraint\LogicalNot as ReverseConstraint;

trait InteractsWithDatabase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupDatabase();
    }

    protected abstract function prepareDatabase(): void;

    protected abstract function cleanupDatabase(): void;

    protected function assertDatabaseHas($table, array $data, $connection = null)
    {
        $this->assertThat(
            $table, new HasInDatabase($this->getConnection($connection), $data)
        );

        return $this;
    }

    protected function assertDatabaseMissing($table, array $data, $connection = null)
    {
        $constraint = new ReverseConstraint(
            new HasInDatabase($this->getConnection($connection), $data)
        );

        $this->assertThat($table, $constraint);

        return $this;
    }

    protected function assertDatabaseCount($table, int $count, $connection = null)
    {
        $this->assertThat(
            $table, new CountInDatabase($this->getConnection($connection), $count)
        );

        return $this;
    }

    protected function getEntitiesFactory(): EntitiesFactoryInterface
    {
        return $this->app->get(EntitiesFactoryInterface::class);
    }

    protected function getConnection($connection = null): DatabaseInterface
    {
        $dbal = $this->app->get(DatabaseManager::class);

        return $connection ?: $dbal->database();
    }
}