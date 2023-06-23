<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Whirlwind\App\Console\Application;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Infrastructure\Repository\ResultFactory;
use Whirlwind\MigrationCore\BlueprintFactoryInterface;
use Whirlwind\MigrationCore\Command\Migration\CreateCommand;
use Whirlwind\MigrationCore\Command\Migration\InstallCommand;
use Whirlwind\MigrationCore\Command\Migration\RollbackCommand;
use Whirlwind\MigrationCore\Command\Migration\StatusCommand;
use Whirlwind\MigrationCore\Domain\Migration;
use Whirlwind\MigrationCore\Domain\MigrationRepositoryInterface;
use Whirlwind\MigrationCore\Infrastructure\Repository\MigrationRepository;
use Whirlwind\MigrationCore\Infrastructure\Repository\MigrationTableGatewayInterface;
use Whirlwind\Persistence\Mongo\ConditionBuilder\ConditionBuilder;
use Whirlwind\Persistence\Mongo\Hydrator\MongoHydrator;
use Whirlwind\Persistence\Mongo\MongoConnection;
use Whirlwind\Persistence\Mongo\Query\MongoQueryFactory;
use WhirlwindFramework\MongoMigrations\Infrastructure\MongoMigrationTableGateway;

class MongoMigrationServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected array $provides = [
        MigrationTableGatewayInterface::class,
        BlueprintFactoryInterface::class,
        MigrationRepositoryInterface::class,
        ConnectionInterface::class,
    ];

    public function provides(string $id): bool
    {
        return \in_array($id, $this->provides);
    }

    public function register(): void
    {
        $this->getContainer()->add(
            MigrationTableGatewayInterface::class,
            fn (): MigrationTableGatewayInterface => new MongoMigrationTableGateway(
                $this->getContainer()->get(MongoConnection::class),
                $this->getContainer()->get(MongoQueryFactory::class),
                $this->getContainer()->get(ConditionBuilder::class),
                'migrations'
            )
        );

        $this->getContainer()->add(
            BlueprintFactoryInterface::class,
            fn (): BlueprintFactoryInterface => new BlueprintFactory()
        );

        $this->getContainer()->add(
            MigrationRepositoryInterface::class,
            fn (): MigrationRepositoryInterface => new MigrationRepository(
                $this->getContainer()->get(MigrationTableGatewayInterface::class),
                $this->getContainer()->get(MongoHydrator::class),
                Migration::class,
                $this->getContainer()->get(ResultFactory::class)
            )
        );

        $this->getContainer()->add(
            ConnectionInterface::class,
            fn (): ConnectionInterface => $this->getContainer()->get(MongoConnection::class)
        );
    }

    public function boot(): void
    {
        /** @var Application $app */
        $app = $this->getContainer()->get(Application::class);
        $app->addCommand('migrate:install', InstallCommand::class);
        $app->addCommand('migrate:create', CreateCommand::class);
        $app->addCommand('migrate:rollback', RollbackCommand::class);
        $app->addCommand('migrate:status', StatusCommand::class);
    }
}
