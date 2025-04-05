<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:country';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import countries from a CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = storage_path('app/public/country_csv/countries.csv');

        if (!file_exists($filePath)) {
            $this->error("File not found at path: {$filePath}");
            return 1;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $records = Statement::create()->process($csv);

        // Get the total number of records for the progress bar
        $totalRecords = $records->count();
        $this->info("Found {$totalRecords} records. Starting import...");

        // Initialize the progress bar
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        $not_created_list = [];

        foreach ($records as $record) {
            try {
                // Country::updateOrCreate(['country_id' => $record['id']], [
                //     'country_id' => $record['id'],
                //     'name' => $record['name'],
                //     'iso3' => $record['iso3'],
                //     'iso2' => $record['iso2'],
                //     'numeric_code' => $record['numeric_code'],
                //     'phone_code' => $record['phone_code'],
                //     'capital' => $record['capital'],
                //     'currency' => $record['currency'],
                //     'currency_name' => $record['currency_name'],
                //     'currency_symbol' => $record['currency_symbol'],
                //     'region' => $record['region'],
                //     'nationality' => $record['nationality'],
                //     'timezones' => json_encode($record['timezones']),
                //     'latitude' => $record['latitude'],
                //     'longitude' => $record['longitude'],
                //     'emoji' => $record['emoji'],
                //     'emojiU' => $record['emojiU'],
                //     'country_type' => 'no',
                // ]);
            } catch (\Exception $e) {
                $this->error("Country create error : {$e}");
                array_push($not_created_list, $record);
            }

            // Advance the progress bar after each record is processed
            $bar->advance();
        }

        // Finish the progress bar
        $bar->finish();

        if (count($not_created_list) > 0) {
            $this->error("Country Note create  record in table (" . count($not_created_list) . ")");
        }

        $this->info('Countries imported successfully!');
        return 0;
    }
}
