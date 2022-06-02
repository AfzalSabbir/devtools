<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

use Config;
class SquashDump extends Command
{
    use Traits\HistoryFilename;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'kda:squash:dump {file} {env}';

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

        if(count($files)>1){
            $toremove = $files->slice(0,count($files)-1);
            $last = $files->last();
            $this->info('we will remove: ');
            dump($toremove);
            $this->info('and keep ');
            dump($last);
            if ($this->confirm('Do you wish to continue?')) {
                foreach ($toremove as $file){
                    unlink($file);
                }
            }
        }else{
            $this->info ('nothing to do');
            dump($files);
        }
    }


 
}
