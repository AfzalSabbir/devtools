<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
class FakeModel
{
}
class MigrateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kda:backpack:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fresh migration';


    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }


    public function fire()
    {
        return $this->handle();
    }
    protected function getOptions()
    {
        return [
            ['stage', 's', InputOption::VALUE_REQUIRED, 'force stage'],
            ['draft', 'd', InputOption::VALUE_REQUIRED, 'draft'],
        ];
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Migrating ');
        $stage =  $this->option('stage');
        
        if($stage===null){
            $stage = config('kda.backpack.devtools.stage','default');
        }


        if(config('kda.backpack.devtools.stages.'.$stage) === null){
            $this->warn('stage '.$stage.' not defined in config, will use default values');
        }

/*

        $seeds =  dev_config($stage,'seeds');
        $has_seeds = false;


        if (is_array($seeds) && sizeof($seeds) > 0) {
            $has_seeds = true;
        }


        if ($has_seeds) {
            foreach ($seeds as $seed) {
                $this->call('kda:dump:table', ['table' => $seed, 'file' => str_replace('_', '', $seed) . '.sql']);
            }
        }
*/
        $this->call('kda:seeds:dump',['--stage' => $stage]);
        $crud = dev_config($stage,'generate_backpack_crud');
        $source =  dev_config($stage,'blueprint.source');
        $namespace = 'App\\' . dev_config($stage,'blueprint.source');
        $path = './app/' . dev_config($stage,'blueprint.source');
        
      /*  if ($this->files->exists('./.blueprint')) {
            if (dev_config($stage,'blueprint.erase')) {
                $this->warn('blueprint already generated - erasing files');
                $this->call('blueprint:erase');
            }
        }

        $this->call('blueprint:build',['overwrite-migration'=>true]);*/

        $this->call('kda:devtools:blueprint',['--stage'=>$stage]);


        foreach (glob($path . '/*.php') as $filename) {
            dump($filename);
            preg_match('/([^\/]*)\.php/', $filename, $match);
            $class = $match[1];
            $nsclass = $namespace . '\\' . $class;
            $model = new $nsclass();
            //  dd($model->getFillable());

            $this->call('kda:scaffold:blueprintmodel', ['table_name' => $class,'source'=>$source,'--crud'=>$crud]);
            $this->call('kda:scaffold:blueprintfactory', ['table_name' => $class]);
            if (dev_config($stage,'generate_backpack_crud')===true) {
                if (dev_config($stage,'use_dynamic_admin_sidebar')) {
                    $this->call('kda:backpack:crud', ['name' => $class]);
                } else {
                    $this->call('backpack:crud', ['name' => $class]);
                }
            }
        }

        //   $this->call('backpack:build');

        $this->call('migrate:fresh', ['--seed' => true]);

        $this->call('kda:seeds:restore');
    }
}
