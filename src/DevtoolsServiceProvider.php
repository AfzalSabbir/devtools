<?php

namespace KDA\Backpack;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;

use KDA\Laravel\PackageServiceProvider;

class DevtoolsServiceProvider extends PackageServiceProvider
{

    use \KDA\Laravel\Traits\HasConfig;
    use \KDA\Laravel\Traits\HasCommands;
    use \KDA\Laravel\Traits\HasHelper;


    protected $configs= [
     'kda/backpack/devtools.php'  =>'kda.backpack.devtools'
    ];

    protected $_commands = [
        Commands\MigrateCommand::class,
        Commands\DumpTable::class,
        Commands\RestoreSeeds::class,
        Commands\DumpSeeds::class,
        Commands\RestoreTable::class,
        Commands\Scaffolders\BlueprintModel::class,
        Commands\Scaffolders\BlueprintFactory::class,
        Commands\Blueprint::class,
        Commands\ListTable::class,
        Commands\SquashDump::class
    ];

    protected function packageBaseDir()
    {
        return dirname(__DIR__, 1);
    }


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       
        $resource_path = dirname(__DIR__, 1) . "/resources";
        $scaffolder_templates = $resource_path."/views/scaffolder";
        $this->loadViewsFrom($scaffolder_templates, "kda/scaffolder");


        File::mixin(new FileMixins());
        return parent::register();
    }

   

}
