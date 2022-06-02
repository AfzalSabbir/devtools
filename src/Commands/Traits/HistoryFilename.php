<?php
namespace KDA\Backpack\Commands\Traits;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

trait HistoryFilename
{

    protected function getLastDumpFilename($filename)
    {
       

        $filesystem = app()->make(Filesystem::class);
        $result = Collection::make(database_path() . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $filename) {
      
                return $filesystem->glob($path . '*_' . $filename);
            })
            ->last();

        return $result ?? false;
    }

    protected function getDumpFilename($filename): string
    {
        $timestamp = date('Y_m_d_His');

        return  database_path() . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR . $timestamp."_".$filename;
        
    }

    protected function getAllFiles($filename){
        $filesystem = app()->make(Filesystem::class);
        return Collection::make(database_path() . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $filename) {
      
                return $filesystem->glob($path . '*_' . $filename);
            });
    }
   
}
