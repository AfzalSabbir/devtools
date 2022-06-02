<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

use Config;
class RestoreTable extends Command
{
    use Traits\HistoryFilename;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'kda:restore:table {file}';

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
        $this->info('Restoring '.$file);
        $__command = "mysql --host=%s --port=%s --user=%s --password=%s --default-character-set=utf8  --init-command=\"SET SESSION FOREIGN_KEY_CHECKS=0;\"  %s< %s";

        $driver = Config::get('database.default', false);
        $db = Config::get('database.connections.' . $driver);

       // dd($this->getLastDumpFilename($file));
      //  dd($driver,$db);
        $last_file = $this->getLastDumpFilename($file);

        $this->info('restoring '.$last_file);
        $command = sprintf(
            $__command,
            escapeshellarg($db['host']),
            escapeshellarg($db['port']),
            escapeshellarg($db['username']),
            escapeshellarg($db['password']),
            escapeshellarg($db['database']),
            escapeshellarg($last_file)

        );
        shell_exec($command);
        
    }


 
}
