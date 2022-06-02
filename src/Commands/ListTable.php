<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

use Config;
class ListTable extends Command
{
    use Traits\HistoryFilename;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'kda:list:table {file} {env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
    
        $file = $this->argument('file');
        $env = $this->argument('env');
        
        $files = $this->getAllFiles($env.'_'.str_replace('_','',$file));

       dump($files);
    }


 
}
