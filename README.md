# Mongo migrations
MongoDB's migration tool

## Implementation notes

1. Bind MigrationPaths dependencies (paths to all your migration folders).
```php
$container->add(
    \Whirlwind\MigrationCore\Config\MigrationPaths::class,
    fn (): \Whirlwind\MigrationCore\Config\MigrationPaths => new \Whirlwind\MigrationCore\Config\MigrationPaths([
        new \Whirlwind\MigrationCore\Config\MigrationPath(
            '/path/to/migrations/folder',
            'Your\\Migration\\Namespace'
        )
    ])
)
```
2. In case of using your own migration template file you have to bind `Config` dependency.
```php
$container->add(
    Whirlwind\MigrationCore\Config\Config::class,
    fn () \Whirlwind\MigrationCore\Config\Config::class => new \Whirlwind\MigrationCore\Config\Config(
        $container->get(\Whirlwind\MigrationCore\Config\MigrationPaths::class),
        '/path/to/your/migration/template'
    )
)
```
3. Register `MongoMigrationServiceProvider` for your console application.
```php
$container->addServiceProvider(new \WhirlwindFramework\MongoMigrations\MongoMigrationServiceProvider())
```

## Commands
After registering `MongoMigrationServiceProvider` you have ability to manage your database migrations by console commands.

### Create migration
Run this command for creating new migration file.
```php
php /path/to/console/executable migrate:create MyMigration
```
The command require migration file name as the first argument. If you would like to specify migration path you can use option
`--path=/my/migration/path`. Pay attention that such path must be registered in `MigrationPaths` dependency.

### Install migration
The command will help you to install migrations.
```php
php /path/to/console/executable migrate:install
```
You can control amount of migration that will be installed in your system by the limitation with the first argument. The value
must be integer >= 0. By default, all migrations are installed.

### Rollback migration
Rollback command helps to cancel changes made by migrations
```php
php /path/to/console/executable migrate:rollback
```
The last migration will be rollback in case of running the command without arguments. You can specify amount of migrations
for rollback by specifying limit with the first argument. Option `--all` will help you to rollback all migrations.

### Status
If you would like to see which migrations have run thus far, you may use the `migrate:status` command
```php
php /path/to/console/executable migrate:status
```
The output will show you 10 last migrations that was applied. You can specify limit with the first argument or with
the option `--all`.

## Migration API

`Migration` has a set of useful methods for managing migration process.
```php
class MyMigration111 extends \Whirlwind\MigrationCore\Migration
{
    public function up(): void {
        $this->createIfNotExists('orders', static function (\WhirlwindFramework\MongoMigrations\Blueprint $b) {
            $b->setCapped(true);
            $b->setMax(100);
            $b->setSize(500);
            $b->setCollation([
                'locale' => 'en',
                'strength' => 1,
            ]);
            $b->setValidator([
                '$jsonSchema' => [
                    'bsonType' => 'object'
                    'required' => ['name', 'year'],
                    'properties' => [
                        'name' => [
                            'bsonType' => 'string',
                        ],
                        'year' => [
                            'bsonType' => 'int',
                            'minimum' => 1990,
                            'maximum' => 3017,
                        ]   
                    ]
                ]
            ]);
            $b->setValidationLevel(\WhirlwindFramework\MongoMigrations\Blueprint::VALIDATION_LEVEL_STRICT);
            $b->setValidationLevel(\WhirlwindFramework\MongoMigrations\Blueprint::VALIDATION_ACTION_ERROR);
        });
        
        $this->modify('orders', static function (\WhirlwindFramework\MongoMigrations\Blueprint $b) {
            $b->createIndex(['year']);
            
            $b->setOption('unique', true);
            $b->createIndex(['name']);
        });
    }
    
    public function down(): void {
        $this->modify('orders', function (\WhirlwindFramework\MongoMigrations\Blueprint $b) {
            $b->dropAllIndexes();
        });
        
        $this->dropIfExists('orders');
    }
}
```
- `create($collection, $callback)` - used for creating new collection. In `callback` you can specify additional options
that will be used during creation.
- `createIfNotExists($collection, $callback)` - works as `create`, but in case of collection existence it skips creation
- `modify($collection, $callback)` - used for modifying collection. In `callback` you can specify additional options and
make extra operations such as renaming collection or creating new indexes. Pay attention if you require different options
for different commands use `modify` method as much as need.
- `drop($collection)` - delete the whole collection
- `dropIfExists($collection)` - same as `delete`, but checks if the collection exist.
### Blueprint Methods
- `create(callable)` - used for configuring creation process.
- `setCapped(bool)` - set `capped` option.
- `setSize(int)` - set `size` option for capped collection.
- `setValidator(array)` - set `validator` option for collection.
- `setValidationLevel(string)` - set `validatorLevel` option for collection. Possible values are `Blueprint::VALIDATION_LEVEL_OFF`
`Blueprint::VALIDATION_LEVEL_STRICT`, `Blueprint::VALIDATION_LEVEL_MODERATE`.
- `setValidationAction(string)` - set `validationAction` option for collection. Possible values are `Blueprint::VALIDATION_ACTION_WARN`,
`Blueprint::VALIDATION_ACTION_ERROR`.
- `setCollation(array)` - set `collatiob` option for collection. Check MongoDB official documentation for more information.
- `setOption(string, mixed)` - set any option by its name.
- `drop()` - remove the whole collection.
- `dropIfExists()` - remove the whole collection if it exists.
- `createIfNotExists(callable)` - configure creation process for collection if it is not exists.
- `createIndex(array, ?string)` - create new index for `keys` with `name`.
- `dropIndex(string)` - delete index by name.
- `dropAllIndexes()` - delete all collection indexes.
- `renameCollection(string)` - rename collection.
- `insert(array)` - insert new document.
- `batchInsert(array)` - insert multiple documents.
- `update(array, array)` - update documents by criteria with new data.
- `delete(array)` - delete documents by criteria.