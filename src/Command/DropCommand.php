<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Command;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\MigrationCore\Blueprint\Command;
use Whirlwind\Persistence\Mongo\MongoConnection;

class DropCommand extends Command
{
    protected bool $isExists = false;
    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection): void
    {
        if ($this->isExists) {
            $document = $connection->getQueryBuilder()->listCollections(['name' => $this->collection]);
            if (!$connection->createCommand($document)->execute()->toArray()) {
                return;
            }
        }
        $document = $connection->getQueryBuilder()->dropCollection($this->collection);
        $connection->createCommand($document)->execute();
    }
}
