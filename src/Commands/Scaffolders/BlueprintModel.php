<?php

namespace KDA\Backpack\Commands\Scaffolders;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\Schema;

class BlueprintModel extends BaseScaffolder
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kda:scaffold:blueprintmodel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scaffold a model from blueprint model';

    /**
     * Path for view
     *
     * @var string
     */
    protected $view = 'blueprintmodel';

    protected $source_class_fullname = '';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getNamespace($rootNamespace)
    {
        return $rootNamespace . '\Models';
    }
    protected function getArguments()
    {
        return [
            ['table_name', InputArgument::REQUIRED, 'Name of the existing table'],
            ['source', InputArgument::REQUIRED, 'Name of source namespace'],
           
        ];
    }
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force will delete files before regenerating model'],
            ['crud','c', InputOption::VALUE_NONE, 'has_crud'],
        ];
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getSourceNamespace($rootNamespace)
    {
        return $rootNamespace . '\\'. $this->source ?? 'BaseModels';
    }

    protected function buildClass()
    {
        return $this->view([
            'namespace' => $this->class_namespace,
            'classname' => $this->class_basename,
            'parentClass' => $this->source_namespace,
            'crud' => $this->option('crud')
        ])->render();
    }

    protected function initNames($table_name, $class_name = null)
    {
        parent::initNames($table_name, $class_name);
        $this->source_namespace = $this->getSourceNamespace(trim($this->getRootNamespace(), '\\'));
        $this->source_class_fullname = $this->source_namespace . '\\'.$this->class_basename;
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $force = $this->option('force');
        $table_name = $this->argument('table_name');
        $this->source = $this->argument('source');
        $this->initNames($table_name);
        /*   var_dump($this->table_name);
           var_dump($this->source_namespace);
           var_dump($this->source_class_fullname);
           var_dump($this->class_fullname);
           var_dump($this->class_basename);
           var_dump($this->class_namespace);
           var_dump($this->path);
          */
        if ($this->generateClass($force)) {
            $this->info('Generating ' . $this->class_fullname . ' finished');
        }
        /*dd($table_name);
           $sm =  Schema::getConnection()->getDoctrineSchemaManager();
           $columns =$sm->listTableColumns($table_name);
           $test =$sm->listTableDetails($table_name);
           dump($test->getPrimaryKeyColumns(), $test->getForeignKeys());*/

/*

           $class = $this->buildClass();
           var_dump($class);
           if ($this->generateClass($force)) {
               $this->info('Generating ' . $this->class_fullname . ' finished');
           }*/
    }
}
