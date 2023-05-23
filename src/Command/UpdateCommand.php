<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Command;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\MigrationCore\Blueprint\Command;
use Whirlwind\Persistence\Mongo\MongoConnection;

class UpdateCommand extends Command
{
    protected array $conditions;
    protected array $document;
    protected array $options = [];
    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection): void
    {
        $connection->createCommand()->update(
            $this->collection,
            $this->conditions,
            $this->document,
            $this->options
        );
    }
}
