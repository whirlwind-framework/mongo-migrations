<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Command;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\MigrationCore\Blueprint\Command;
use Whirlwind\Persistence\Mongo\MongoConnection;

class RenameCollectionCommand extends Command
{
    protected string $to;
    protected bool $dropTarget = false;

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection): void
    {
        $connection->createCommand(
            [
                'renameCollection' => $this->collection,
                'to' => $this->to,
                'dropTarget' => $this->dropTarget,
            ]
        )->execute();
    }
}
