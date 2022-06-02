<?php

namespace KDA\Backpack\Commands\Scaffolders;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\Schema;

class BlueprintFactory extends BaseScaffolder
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kda:scaffold:blueprintfactory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scaffold a factory from blueprint factory';

    /**
     * Path for view
     *
     * @var string
     */
    protected $view = 'blueprintfactory';

    protected $source_class_fullname = '';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getNamespace($rootNamespace)
    {
        return  'Database\Factories\Real';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getSourceNamespace($rootNamespace)
    {
        return   'Database\Factories';
    }

    protected function buildClass()
    {
        return $this->view([
            'namespace' => $this->class_namespace,
            'classname' => $this->class_basename,
            'parentClass' => $this->source_namespace,
            'model' => $this->model_name
        ])->render();
    }
    public function getRootNamespace()
    {
        return 'Database';
    }
    protected function initNames($table_name, $class_name = null)
    {
        parent::initNames($table_name, $class_name);

        $this->source_namespace = $this->getSourceNamespace(trim($this->getRootNamespace(), '\\'));
        $this->source_class_fullname = $this->source_namespace . '\\'.$this->class_basename;
    }

    public function getPathFromClassName()
    {
       return 'database/factories/Real/'.$this->class_basename.'.php';
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
       
        $this->initNames($table_name.'Factory');
        $factory = new $this->source_class_fullname() ;

        $this->model_name = 'protected $model = \App\\Models\\'.$table_name.'::class;';
     //   dd($factory);
        /*   var_dump($this->table_name);
           var_dump($this->source_namespace);
           var_dump($this->source_class_fullname);
           var_dump($this->class_fullname);
           var_dump($this->class_basename);
           var_dump($this->class_namespace);
           var_dump($this->path);
          */
       //   $this->info('Generating ' . $this->class_fullname . ' finished');
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
