<?php

namespace KDA\Backpack\Commands;

use GKA\Noctis\Providers\AuthProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

use Config;

class DumpTable extends Command
{
    use Traits\HistoryFilename;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'kda:dump:table {table} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $table = $this->argument('table');
        $file = $this->argument('file');
        $this->info('Dumping ' . $table . ' to ' . $file);
        $__command = "mysqldump --host=%s --complete-insert --port=%s --user=%s --password=%s --no-tablespaces --no-create-info --no-create-db --default-character-set=utf8 --compact %s %s";

        $driver = Config::get('database.default', false);
        $db = Config::get('database.connections.' . $driver);
        //  dd($driver,$db);
        $command = sprintf(
            $__command,
            escapeshellarg($db['host']),
            escapeshellarg($db['port']),
            escapeshellarg($db['username']),
            escapeshellarg($db['password']),
            escapeshellarg($db['database']),
            escapeshellarg($table),
            //  escapeshellarg($this->getDumpFilename($file))

        );
        $result = shell_exec($command);
        $checksum = md5($result);
        if(trim($result) ===''  && config('kda.backpack.devtools.dump_empty_seeds') === false){
            $this->warn('not dumped because it would result in empty seed');

            return;
        }

       


        if (($src = $this->getLastDumpFilename($file)) !== false) {

            $compare = md5_file($src);

            if ($checksum !== $compare) {
                $dest = $this->getDumpFilename($file);
                $this->info('dumped file into ' . $dest);
                file_put_contents($dest, $result);
            } else {
                $this->warn('not dumped because table content has not changed');
            }
        }else{
           
            $dest = $this->getDumpFilename($file);
            if(!$this->files->exists(dirname($dest))){

                $this->files->makeDirectory(dirname($dest), 0744, true, true);
        
            }


            $this->warn('never dumped, dumping into '.$dest);

            file_put_contents($dest, $result);
        }
    }
}
