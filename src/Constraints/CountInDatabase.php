<?php

declare(strict_types=1);

namespace Hotrush\Spiral\Testing\Constraints;

use PHPUnit\Framework\Constraint\Constraint;
use Spiral\Database\DatabaseInterface;

class CountInDatabase extends Constraint
{
    protected DatabaseInterface $database;

    protected int $expectedCount;

    protected int $actualCount;

    public function __construct(DatabaseInterface $database, int $expectedCount)
    {
        $this->database = $database;
        $this->expectedCount = $expectedCount;
    }

    public function matches($table): bool
    {
        $this->actualCount = $this->database->select()->from($table)->count();

        return $this->actualCount === $this->expectedCount;
    }

    public function failureDescription($table): string
    {
        return sprintf(
            "table [%s] matches expected entries count of %s. Entries found: %s.\n",
            $table, $this->expectedCount, $this->actualCount
        );
    }

    public function toString($options = 0): string
    {
        return (new \ReflectionClass($this))->name;
    }
}