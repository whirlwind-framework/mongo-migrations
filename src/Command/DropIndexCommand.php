<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Command;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\MigrationCore\Blueprint\Command;
use Whirlwind\Persistence\Mongo\MongoConnection;

class DropIndexCommand extends Command
{
    protected string $name;

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection): void
    {
        $connection->createCommand(
            $connection->getQueryBuilder()->dropIndexes(
                $this->collection,
                $this->name
            )
        )->execute();
    }
}
