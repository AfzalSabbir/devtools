<?php

namespace KDA\Backpack\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class HistoryFileSeeder extends Seeder
{
    use \KDA\Backpack\Commands\Traits\HistoryFilename;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if(!property_exists($this,'history')){
            throw new \Error('you have to define history property');
        }
        

        if( ($file = $this->getLastDumpFilename($this->history.'.sql')) !== false){
            $sql = file_get_contents($file);
            if(!empty($sql))
            DB::statement($sql);
        }
    
  

    
    }


}
