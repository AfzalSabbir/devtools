<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class RestoreSeeds extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kda:seeds:restore';

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
            ['force', 'f', InputOption::VALUE_NONE, 'Force will ignore config'],
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

        $seeds =  config('kda.backpack.devtools.seeds');
        $force = $this->option('force');
        $has_seeds = false;
        if (is_array($seeds) && sizeof($seeds) > 0) {
            $has_seeds = true;
        }
        $should_restore_seeds = config('kda.backpack.devtools.should_restore_seeds', false);


        if ($has_seeds && ($should_restore_seeds || $force===true) ) {
            if($force ===true){
                $this->warn('forcing restoring seeds');
            }
            foreach ($seeds as $seed) {
                $this->call('kda:restore:table', ['file' => str_replace('_','',$seed) . '.sql']);
            }
        } else if ($has_seeds) {
            $this->warn('seeds weren\'t restored due to config, use --force to override');
        }
    }
}
