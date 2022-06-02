<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class Blueprint extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kda:devtools:blueprint';

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
            ['erase', 'e', InputOption::VALUE_NONE, 'erase'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $stage =  $this->option('stage');
       /* $erase = dev_config($stage,'blueprint.erase',$this->option('erase')?? true); 
        if ($this->files->exists('./.blueprint')) {
            if ($erase) {
                $this->warn('blueprint already generated - erasing files');
                $this->call('blueprint:erase');
            }
        }*/

        $this->call('blueprint:build',['--overwrite-migrations'=>true]);

    }
}
