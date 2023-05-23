<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations\Infrastructure;

use Whirlwind\MigrationCore\Infrastructure\Repository\MigrationTableGatewayInterface;
use Whirlwind\Persistence\Mongo\MongoTableGateway;

class MongoMigrationTableGateway extends MongoTableGateway implements MigrationTableGatewayInterface
{
    public function queryOrCreateCollection(array $conditions = [], int $limit = 0, array $order = []): array
    {
        $collection = $this->connection->createCommand(
            $this->connection->getQueryBuilder()->listCollections(['name' => $this->collectionName])
        )->execute()->toArray();
        if (!$collection) {
            $this->connection->createCommand(
                $this->connection->getQueryBuilder()->createCollection($this->collectionName)
            )->execute();
        }

        return $this->queryAll($collection, $order,  $limit);
    }
}
