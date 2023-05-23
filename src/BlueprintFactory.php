<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations;

use Whirlwind\MigrationCore\BlueprintFactoryInterface;
use Whirlwind\MigrationCore\BlueprintInterface;

class BlueprintFactory implements BlueprintFactoryInterface
{
    public function create(string $collection): BlueprintInterface
    {
        return new Blueprint($collection);
    }
}
