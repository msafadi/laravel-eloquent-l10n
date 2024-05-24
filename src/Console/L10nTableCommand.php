<?php

namespace Safadi\Eloquent\L10n\Console;

use Illuminate\Console\MigrationGeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:l10n-table', aliases: ['l10n:table'])]
class L10nTableCommand extends MigrationGeneratorCommand
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l10n-table
                            {model? : The name of the model or parent table}
                            {--database= : The database connection}';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:l10n-table';

    /**
     * The console command name aliases.
     *
     * @var array
     */
    protected $aliases = ['l10n:table'];
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the model localization/translations table';

    protected $rootNamespace = 'App\\';

    /**
     * Get the migration table name.
     *
     * @return string
     */
    protected function migrationTableName()
    {
        $model = $this->qualifyModel($this->argument('model'));
        $postfix = config('eloquent-l10n.table_postfix', '_l10n');
        if (class_exists($model)) {
            $instance = new $model();
            if (method_exists($instance, 'getL10nTable')) {
                return $instance->getL10nTable();
            }
            return $instance->getTable() . $postfix;
        }
        // Treat model as the table name
        return $this->argument('model') . $postfix;
    }

    /**
     * Get the path to the migration stub file.
     *
     * @return string
     */
    protected function migrationStubFile()
    {
        return __DIR__.'/stubs/l10n.stub';
    }

    /**
     * Replace the placeholders in the generated migration file.
     *
     * @param  string  $path
     * @param  string  $table
     * @return void
     */
    protected function replaceMigrationPlaceholders($path, $table)
    {
        $columns = $this->schemaColumns();
        $foreignId = $this->foreignKeyName();
        $stub = str_replace(
            ['{{table}}', '{{foreignKey}}', '{{columns}}'], 
            [$table, $foreignId, $columns], 
            $this->files->get($this->migrationStubFile())
        );

        $this->files->put($path, $stub);
    }

    protected function foreignKeyName()
    {
        $model = $this->qualifyModel($this->argument('model'));
        if (class_exists($model)) {
            $instance = new $model();
            if (method_exists($instance, 'getL10nForigenKeyName')) {
                return $instance->getL10nForigenKeyName();
            }
            return $instance->getForeignKey();
        }
        return Str::snake(Str::singular($this->argument('model'))) . '_id';
    }

    protected function schemaColumns()
    {
        return '';
    }

    /**
     * Qualify the given model class base name.
     *
     * @param  string  $model
     * @return string
     */
    protected function qualifyModel(string $model)
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();
        $model = Str::studly($model);

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
                    ? $rootNamespace.'Models\\'.$model
                    : $rootNamespace.$model;
    }

    public function rootNamespace($namespace = null)
    {
        if (null === $namespace) {
            return $this->rootNamespace;
        }
        $this->rootNamespace = rtrim($namespace, '\\') . '\\';
        return $this;
    }
}