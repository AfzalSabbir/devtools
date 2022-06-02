# backpack-devtools

## about

Our devtools helps to iterate quickly over models without too much of a hassle

This is done by several points: 

* Providing an unique command line that performs all the operations
    
             sail artisan kda:backpack:migrate

* Providing a way to regenerate models without breaking changes (wip)

* Provide a way to automatically save and restore seeds in sql files with an history

* Provide a way to easily write generate diff migrations  (wip)


## install

    sail composer require fdt2k/backpack-devtools --prefer-source


## Blueprint Installation
optional
    sail artisan vendor:publish --tag=blueprint-config

### configure blueprint

edit config/blueprint.php 

set

        'models_namespace' => 'BaseModels',

or whatever other namespace you want.


then replace the model migration generator with our own

    'migration' => \KDA\Backpack\BlueprintMigrationGenerator::class,

in config/blueprint.php

#### model namespace
If you changed the namespace to other than BaseModels, you'll have to configure devtools

    sail artisan vendor:publish --provider="KDA\Backpack\DevtoolsServiceProvider" --tag="config"

then change it in config/kda/backpack/devtools.php

    'blueprint' => [
        'source'=> 'BaseModels',
        ]





## Automatic Seeding configuration 

### automatic seeding

Automatic seeding helps during development, by automatically dumping specified tables and restoring them after the migration. 

just put table into the settings 

    'seeds'=>[
        'sidebars',
        'users',
        'user_custom_lists',
    ],

## without Seeders

by default you have not to worry about anything, kda:backpack:migrate will automatically reseeds with sql dumps



## with Seeders

disable autorestore on migrate by setting

    'should_restore_seeds'=> false,

then create a seeder like this 

    <?php

    namespace Database\Seeders;


    class SidebarSeeder extends \KDA\Backpack\Database\Seeders\HistoryFileSeeder
    {
    
        protected $history = 'sidebars';
    }

Use as any other Seeder
Please note that autoseeding only works with mysql, because I'm lazy 


# Factory namespace

by default laravel-shift/blueprint erase factories so we just have to add 
    
    use Illuminate\Database\Eloquent\Factories\Factory;

... 

    Factory::guessFactoryNamesUsing(function (string $modelName) {
        return 'Database\Factories\\Real\\'.class_basename($modelName).'Factory';
    });

to app/Providers/AppServiceProvider.php

    $this->app->singleton(\Faker\Generator::class, function () {
        return \Faker\Factory::create('fr_CH');
    });