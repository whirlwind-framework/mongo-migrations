<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Command;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\MigrationCore\Blueprint\Command;
use Whirlwind\Persistence\Mongo\MongoConnection;

class InsertCommand extends Command
{
    protected bool $isBatch = false;
    protected array $data;
    protected array $options = [];

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection): void
    {
        if ($this->isBatch) {
            $connection->createCommand()->batchInsert(
                $this->collection,
                $this->data,
                $this->options
            );
        } else {
            $connection->createCommand()->insert($this->collection, $this->data, $this->options);
        }
    }
}
