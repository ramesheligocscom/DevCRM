<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use App\Models\State;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import states from a CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = storage_path('app/public/country_csv/states.csv');

        if (!file_exists($filePath)) {
            $this->error("File not found at path: {$filePath}");
            return 1;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0); // Assumes first row is the header

        $records = Statement::create()->process($csv);

        // Get the total number of records for the progress bar
        $totalRecords = $records->count();
        $this->info("Found {36} records. Starting import...");

        // Initialize the progress bar
        $bar = $this->output->createProgressBar(36);
        $bar->start();

        $not_created_list = [];

        foreach ($records as $record) {
            if ((int)$record['country_id'] == 101) {
                // $country_id = Country::where('country_id', $record['country_id'])->pluck('id')->first();
                try {
                    // State::create([
                    //     'name' => $record['name'],
                    //     'country_id' => $country_id,
                    //     'country_name' => $record['country_name'],
                    //     'state_code' => $record['state_code'],
                    //     'type' => $record['type'],
                    //     'latitude' => $record['latitude'],
                    //     'longitude' => $record['longitude'],
                    //     'state_type' => 'no'
                    // ]);
                } catch (\Exception $e) {
                    $this->error("State create error : {$e}");
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

        $this->info("\nStates imported successfully!");
        return 0;
    }
}
