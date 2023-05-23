<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Command;

use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\MigrationCore\Blueprint\Command;
use Whirlwind\Persistence\Mongo\MongoConnection;

class CreateIndexCommand extends Command
{
    protected array $keys;
    protected ?string $name = null;
    protected array $options;

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection): void
    {
        $connection->createCommand(
            $connection->getQueryBuilder()->createIndexes(
                $connection->getDefaultDatabaseName(),
                $this->collection,
                [
                    [
                        'key' => $this->keys,
                        'name' => $this->name,
                    ] + $this->options
                ]
            )
        )->execute();
    }

}
