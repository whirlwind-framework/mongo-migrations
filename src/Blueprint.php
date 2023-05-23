<?php

declare(strict_types=1);

namespace WhirlwindFramework\MongoMigrations;

use WhirlwindFramework\MongoMigrations\Command\CreateCommand;
use WhirlwindFramework\MongoMigrations\Command\CreateIndexCommand;
use WhirlwindFramework\MongoMigrations\Command\DeleteCommand;
use WhirlwindFramework\MongoMigrations\Command\DropCommand;
use WhirlwindFramework\MongoMigrations\Command\DropIndexCommand;
use WhirlwindFramework\MongoMigrations\Command\InsertCommand;
use WhirlwindFramework\MongoMigrations\Command\RenameCollectionCommand;
use WhirlwindFramework\MongoMigrations\Command\UpdateCommand;

class Blueprint extends \Whirlwind\MigrationCore\Blueprint
{

    public const VALIDATION_LEVEL_OFF = 'off';
    public const VALIDATION_LEVEL_STRICT = 'strict';
    public const VALIDATION_LEVEL_MODERATE = 'moderate';

    public const VALIDATION_ACTION_WARN = 'warn';
    public const VALIDATION_ACTION_ERROR = 'error';

    protected array $options = [];
    public function create(callable $callback): void
    {
        $callback($this);
        $this->prependCommand(new CreateCommand($this->collection, ['options' => $this->options]));
    }

    public function setCapped(bool $isCapped): void
    {
        $this->options['capped'] = $isCapped;
    }

    /**
     * Specify a maximum size in bytes for a capped collection.
     * @param int $size
     * @return void
     */
    public function setSize(int $size): void
    {
        $this->options['size'] = $size;
    }

    /**
     * The maximum number of documents allowed in the capped collection.
     * @param int $max
     * @return void
     */
    public function setMax(int $max): void
    {
        $this->options['max'] = $max;
    }

    public function setValidator(array $rules): void
    {
        $this->options['validator'] = $rules;
    }

    /**
     * Determines how strictly MongoDB applies the validation rules to existing documents during an update.
     *
     * @param string $level
     * @return void
     */
    public function setValidationLevel(string $level): void
    {
        if (!\in_array(
            $level,
            [self::VALIDATION_LEVEL_OFF, self::VALIDATION_LEVEL_STRICT, self::VALIDATION_LEVEL_MODERATE])
        ) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Invalid value %s. Possible values are %s, %s, %s.',
                    $level,
                    self::VALIDATION_LEVEL_OFF,
                    self::VALIDATION_LEVEL_STRICT,
                    self::VALIDATION_LEVEL_MODERATE
                )
            );
        }
        $this->options['validationLevel'] = $level;
    }

    public function setValidationAction(string $action): void
    {
        if (!\in_array(
            $action,
            [self::VALIDATION_ACTION_WARN, self::VALIDATION_ACTION_ERROR])
        ) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Invalid value %s. Possible values are %s, %s.',
                    $action,
                    self::VALIDATION_ACTION_WARN,
                    self::VALIDATION_ACTION_ERROR
                )
            );
        }
        $this->options['validationAction'] = $action;
    }

    /**
     * The collation option has the following syntax:
     * {
     *   locale: <string>,
     *   caseLevel: <boolean>,
     *   caseFirst: <string>,
     *   strength: <int>,
     *   numericOrdering: <boolean>,
     *   alternate: <string>,
     *   maxVariable: <string>,
     *   backwards: <boolean>
     * }
     * @param array $collation
     * @return void
     */
    public function setCollation(array $collation): void
    {
        $this->options['collation'] = $collation;
    }

    public function setOption(string $name, mixed $value): void
    {
        $this->options[$name] = $value;
    }

    public function drop(): void
    {
        $this->addCommand(new DropCommand($this->collection));
    }

    public function dropIfExists(): void
    {
        $this->addCommand(new DropCommand($this->collection, ['isExists' => true]));
    }

    public function createIfNotExists(callable $callback): void
    {
        $callback($this);
        $this->prependCommand(
            new CreateCommand($this->collection, ['options' => $this->options, 'isNotExists' => true])
        );
    }

    public function createIndex(array $keys, ?string $name = null): void
    {
        $this->addCommand(
            new CreateIndexCommand(
                $this->collection,
                [
                    'keys' => $keys,
                    'name' => $name,
                    'options' => $this->options
                ]
            )
        );
    }

    public function dropIndex(string $name): void
    {
        $this->addCommand(new DropIndexCommand($this->collection, ['name' => $name]));
    }

    public function dropAllIndexes(): void
    {
        $this->addCommand(new DropIndexCommand($this->collection, ['name' => '*']));
    }

    public function renameCollection(string $to): void
    {
        $this->addCommand(new RenameCollectionCommand($this->collection, ['to' => $to] + $this->options));
    }

    public function insert(array $data): void
    {
        $this->addCommand(new InsertCommand($this->collection, ['data' => $data, 'options' => $this->options]));
    }

    public function batchInsert(array $items): void
    {
        $this->addCommand(new InsertCommand(
            $this->collection,
            [
                'data' => $items,
                'isBatch' => true,
                'options' => $this->options,
            ]
        ));
    }

    public function update(array $conditions, array $document): void
    {
        $this->addCommand(new UpdateCommand(
            $this->collection,
            \compact('conditions', 'document') + ['options' => $this->options]
        ));
    }

    public function delete(array $conditions): void
    {
        $this->addCommand(new DeleteCommand(
            $this->collection,
            [
                'conditions' => $conditions,
                'options' => $this->options
            ]
        ));
    }
}
