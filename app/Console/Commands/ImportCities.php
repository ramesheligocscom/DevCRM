<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import cities from a CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = storage_path('app/public/country_csv/cities.csv');

        if (!file_exists($filePath)) {
            $this->error("File not found at path: {$filePath}");
            return 1;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0); // Assumes first row is the header

        $records = Statement::create()->process($csv);

        // Get the total number of records for the progress bar
        $totalRecords = $records->count();
        $this->info("Found {4205} records. Starting import...");

        // Initialize the progress bar
        $bar = $this->output->createProgressBar(4205);
        $bar->start();

        $not_created_list = [];

        foreach ($records as $record) {
            if ((int)$record['country_id'] == 101) {
                // $country_id = Country::where('country_id', $record['country_id'])->pluck('id')->first();
                // $state_id = State::whereHas('country', function ($que) use ($country_id) {
                //     $que->where('id', $country_id);
                // })->where('name', $record['state_name'])->pluck('id')->first();
                try {
                    // City::create([
                    //     'name' => $record['name'],
                    //     'state_id' => $state_id,
                    //     'state_name' => $record['state_name'],
                    //     'country_id' => $country_id,
                    //     'country_name' => $record['country_name'],
                    //     'latitude' => $record['latitude'],
                    //     'longitude' => $record['longitude'],
                    //     'city_type' => 'no',
                    // ]);
                } catch (\Exception $e) {
                    $this->error("City create error : {$e}");
                    array_push($not_created_list, $record);
                }
                // Advance the progress bar after each record is processed
                $bar->advance();
            }
        }

        // Finish the progress bar
        $bar->finish();

        if (count($not_created_list) > 0) {
            $this->error("Country Note create  record in table (" . count($not_created_list) . ")");
        }

        $this->info("\nCities imported successfully!");
        return 0;
    }
}
