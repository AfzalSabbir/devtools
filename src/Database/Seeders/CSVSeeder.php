<?php
  
namespace KDA\Backpack\Database\Seeders;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Author;
use App\Models\Editor;
use Illuminate\Support\Collection;
abstract class CSVSeeder extends Seeder
{

    abstract public function beforeRun();

    abstract public function handleRow($row,$raw,$map);

    abstract public function handleMap($line);

    abstract public function getFile();

    abstract public function afterRun();


    protected function getLastDumpFilename($filename,$folder='datasamples/csv')
    {
       

        $filesystem = app()->make(Filesystem::class);
        $path = base_path() . DIRECTORY_SEPARATOR . $folder. DIRECTORY_SEPARATOR;
        $result = Collection::make($path)
            ->flatMap(function ($path) use ($filesystem, $filename) {
                return $filesystem->glob($path . '*_' . $filename);
            })
            ->last();

        return $result ?? false;
    }

    public function hasHeaders(){
        if (!property_exists($this, 'has_headers')) {
            return true;
        }else {
            return $this->has_headers;
        }

    }

    abstract public function getMap();
    public function getSeparator(){
        if (!property_exists($this, 'csv_separator')) {
            return ";";
        }else {
            return $this->csv_separator;
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Book::truncate();
        $this->beforeRun();

        dump('seeding file '.$this->getFile());
        $csvFile = fopen($this->getFile(), "r");
        $firstline = $this->hasHeaders();
        $map = $this->getMap();
        while (($data = fgetcsv($csvFile, 4000, $this->getSeparator())) !== FALSE) {
            if($firstline){
                $map = $this->handleMap($data);
            }else if (!$firstline) {
                
                $row = collect($data)->reduce(function ($carry, $value, $key) use ($map) {
                    $carry[$map[$key]]= $value;
                    return $carry;
                },[]);
                $this->handleRow($row,$data,$map);

                
            }
            $firstline = false;
        }
   
        fclose($csvFile);
        $this->afterRun();
    }
}