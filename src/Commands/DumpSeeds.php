<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class DumpSeeds extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kda:seeds:dump';

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
            ['stage', 's', InputOption::VALUE_REQUIRED, 'force stage'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('dumping seeds ');
        $stage =  $this->option('stage') ?? 'default';
       
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
    }
}
