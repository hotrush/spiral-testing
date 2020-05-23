<?php

declare(strict_types=1);

namespace Hotrush\Spiral\Testing\Constraints;

use PHPUnit\Framework\Constraint\Constraint;
use Spiral\Database\DatabaseInterface;

class HasInDatabase extends Constraint
{
    protected DatabaseInterface $database;

    protected array $data;

    public function __construct(DatabaseInterface $database, array $data)
    {
        $this->database = $database;
        $this->data = $data;
    }

    public function matches($table): bool
    {
        return $this->database->select()->from($table)->where($this->data)->count() > 0;
    }

    public function failureDescription($table): string
    {
        return sprintf(
            "a row in the table [%s] matches the attributes %s.\n\n",
            $table, $this->toString(JSON_PRETTY_PRINT)
        );
    }

    public function toString($option = 0): string
    {
        return json_encode($this->data, $option);
    }
}