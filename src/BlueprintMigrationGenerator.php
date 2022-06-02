<?php

namespace KDA\Backpack;

use Blueprint\Blueprint;
use Blueprint\Contracts\Generator;
use Blueprint\Models\Model;
use Blueprint\Tree;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Filesystem\Filesystem;

use Blueprint\Generators\MigrationGenerator;

class BlueprintMigrationGenerator extends MigrationGenerator
{

    protected $output = [];

    private $hasForeignKeyConstraints = false;

    private  $migrationPath = 'database/migrations/';


    public function output(Tree $tree, $overwrite = false): array
    {

        $this->shouldOverwrite = $overwrite;

        $tables = ['tableNames' => [], 'pivotTableNames' => [], 'polymorphicManyToManyTables' => []];

        $stub = $this->filesystem->stub('migration.stub');
        /**
         * @var \Blueprint\Models\Model $model
         */
        
        foreach ($tree->models() as $model) {

            if($this->isAnUpdate($model->tableName())){
                $stub = $this->filesystem->kda_stub(dirname(__DIR__,1).'/stubs/migration_update.stub');
            }

            $tables['tableNames'][$model->tableName()] = $this->populateStub($stub, $model);

            if (!empty($model->pivotTables())) {
                foreach ($model->pivotTables() as $pivotSegments) {
                    $pivotTableName = $this->getPivotTableName($pivotSegments);
                    $tables['pivotTableNames'][$pivotTableName] = $this->populatePivotStub($stub, $pivotSegments);
                }
            }

            if (!empty($model->polymorphicManyToManyTables())) {
                foreach ($model->polymorphicManyToManyTables() as $tableName) {
                    $tables['polymorphicManyToManyTables'][Str::lower(Str::plural(Str::singular($tableName) . 'able'))] = $this->populatePolyStub($stub, $tableName);
                }
            }
        }

        return $this->createMigrations($tables, $overwrite);
    }


   

    protected function getClassName(Model $model)
    {
        if($this->isAnUpdate($model->tableName())){
            return 'Update' . Str::studly($model->tableName()) . 'Table'.$this->guessMigrationCount($model->tableName());
        }
        return 'Create' . Str::studly($model->tableName()) . 'Table';
    }

   

    protected function getMigrationFileNameCreate ($tableName) {
        return '_create_' . $tableName . '_table.php';
    }
    
    protected function getMigrationFileNameUpdate ($tableName,$count=0) {
        return '_update_' . $tableName . '_table_'.$count.'.php';
    }

    protected function guessMigrationCount ($tableName){
        $dir = $this->migrationPath;
        $name = $this->getMigrationFileNameCreate($tableName);
        $updates = $this->getMigrationFileNameCreate($tableName);
        
        $migrations = collect($this->filesystem->files($dir))
                ->filter(
                    function (SplFileInfo $file) use ($name) {
                        return str_contains($file->getFilename(), $name);
                    }
                )
                ->sort();

        $migrations2 = collect($this->filesystem->files($dir))
        ->filter(
            function (SplFileInfo $file) use ($updates) {
                return str_contains($file->getFilename(), $updates);
            }
        )
        ->sort();
        return $migrations->count()+$migrations2->count();
    }
    /* check if we should use an update migration */
    protected function isAnUpdate($tableName)
    {

        $overwrite = $this->shouldOverwrite;
        $dir = $this->migrationPath;
        $name = $this->getMigrationFileNameCreate($tableName);

        if ($overwrite) {
            return false;
        } else {
            $migrations = collect($this->filesystem->files($dir))
                ->filter(
                    function (SplFileInfo $file) use ($name) {
                        return str_contains($file->getFilename(), $name);
                    }
                )
                ->sort();
            if ($migrations->isNotEmpty()) {
                return true;
            }
        }

        return false;
    }


  
    protected function getTablePath($tableName, Carbon $timestamp, $overwrite = false)
    {
        $dir = $this->migrationPath;
        $name = $this->getMigrationFileNameCreate($tableName);

        if ($overwrite) {
            $migrations = collect($this->filesystem->files($dir))
                ->filter(
                    function (SplFileInfo $file) use ($name) {
                        return str_contains($file->getFilename(), $name);
                    }
                )
                ->sort();

            if ($migrations->isNotEmpty()) {
                $migration = $migrations->first()->getPathname();

                $migrations->diff($migration)
                    ->each(
                        function (SplFileInfo $file) {
                            $path = $file->getPathname();

                            $this->filesystem->delete($path);

                            $this->output['deleted'][] = $path;
                        }
                    );

                return $migration;
            }
        } else {
            $migrations = collect($this->filesystem->files($dir))
                ->filter(
                    function (SplFileInfo $file) use ($name) {
                        return str_contains($file->getFilename(), $name);
                    }
                )
                ->sort();
            if ($migrations->isNotEmpty()) {
                $name = '_update_' . $tableName . '_table.php';
                $name = $this->getMigrationFileNameUpdate($tableName,$this->guessMigrationCount($tableName));
            }
        }

        return $dir . $timestamp->format('Y_m_d_His') . $name;
    }

    private function shouldAddForeignKeyConstraint(\Blueprint\Models\Column $column)
    {
        if ($column->name() === 'id') {
            return false;
        }

        if ($column->isForeignKey()) {
            return true;
        }

        return config('blueprint.use_constraints')
            && ($this->isIdOrUuid($column->dataType()) && Str::endsWith($column->name(), '_id'));
    }

}
