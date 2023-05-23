<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Command;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\MigrationCore\Blueprint\Command;
use Whirlwind\Persistence\Mongo\MongoConnection;

class CreateCommand extends Command
{
    protected array $options = [];
    protected bool $isNotExists = false;

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection): void
    {
        if ($this->isNotExists) {
            $collection = $connection->createCommand(
                $connection->getQueryBuilder()->listCollections(['name' => $this->collection])
            )->execute()->toArray();
            if ($collection) {
                return;
            }
        }
        $connection->createCommand(
            $connection->getQueryBuilder()->createCollection($this->collection, $this->options)
        )->execute();
    }
}
