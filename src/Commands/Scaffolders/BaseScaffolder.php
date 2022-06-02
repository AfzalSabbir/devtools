<?php

namespace KDA\Backpack\Commands\Scaffolders;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;

class BaseScaffolder extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */

    protected $view_prefix= 'kda/scaffolder::';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $table_name = null;
    protected $root_namespace = null;
    
    protected $class_fullname = null;
    protected $class_basename = null;
    protected $class_namespace = null;
    protected $path = null;
    


    /**
    * Create a new controller creator command instance.
    *
    * @param  \Illuminate\Filesystem\Filesystem  $files
    */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    protected function view($args)
    {
        return view($this->view_prefix.$this->view, $args);
    }

    /**
       * Get user options.
       */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force will delete files before regenerating model'],
        ];
    }
    protected function getArguments()
    {
        return [
            ['table_name', InputArgument::REQUIRED, 'Name of the existing table'],
        ];
    }

    public function fire()
    {
        return $this->handle();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
    }

   
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return $this->laravel->getNamespace();
    }


    /**
    * Get the default namespace for the class.
    *
    * @param  string  $rootNamespace
    * @return string
    */
    protected function getNamespace($root_namespace)
    {
        return $root_namespace;
    }

    public function getPathFromClassName()
    {
        $path = str_replace('\\', '/', $this->class_fullname).".php";

        return preg_replace('|^App/|', 'app/', $path);
    }


    public function generateClassNameFromTable()
    {
        return Str::studly(Str::singular($this->table_name));
    }


    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    public function getFullClassName($name)
    {
        $name = str_replace('/', '\\', $name);

        $root_namespace = $this->getRootNamespace();

        if (Str::startsWith($name, $root_namespace)) {
            return $name;
        }
       

        return $this->getFullClassName(
            $this->getNamespace(trim($root_namespace, '\\')) . '\\' . $name
        );
    }

   
    protected function initNames($table_name, $class_name = null)
    {
        $this->table_name = $table_name;

        if (empty($class_name)) {
            $class_name = $this->generateClassNameFromTable();
        }

        $this->class_basename = $class_name;

        $this->class_fullname = $this->getFullClassName($this->class_basename);

        //  $this->class_basename = class_basename($this->class_fullname);
        $this->class_namespace = Str::replaceLast("\\" . $this->class_basename, '', $this->class_fullname);

        $this->path = $this->getPathFromClassName();
    }

    protected function generateClass($force = false)
    {
        if (! $this->files->isDirectory(dirname($this->path))) {
            $this->files->makeDirectory(dirname($this->path), 0777, true, true);
        }

        if ($this->files->exists($this->path)) {
            if ($force) {
                $this->warn('File '.$this->path.' already exists! File will be deleted.');
                $this->files->delete($this->path);
            } else {
                $this->error('File '.$this->path.' already exists! Use --force to overwrite');
                return false;
            }
        }
        $this->files->put($this->path, $this->buildClass());
        return true;
    }
}
