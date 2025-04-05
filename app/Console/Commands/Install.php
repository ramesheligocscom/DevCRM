<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'module:install {db=refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates Database , Seeds Database , Sets in env';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->argument('db');
        $acceptedArgs = ['fresh', 'refresh'];

        if (in_array($type, $acceptedArgs)) {
            $this->line('Received Type' . $this->argument('db'));
            $this->generateAppKey();
            if ($type == "fresh") $this->clearExportsFolder();
            $this->setupDatabase($type);
            $this->seedDatabase();
            $this->storageLink();
            # $this->setupCountry();
            $this->clearCache();
            $this->envUpdateCmd();
            $this->info('Installation Complete');
        } else {
            $this->error('Invalid Argument ' . $type);
        }
    }

    /**
     * Clears all files and folders inside exports directory.
     */
    protected function clearExportsFolder()
    {
        $exportPath = storage_path('app/public/exports');

        if (File::exists($exportPath)) {
            File::cleanDirectory($exportPath);
            $this->info('Exports folder cleared successfully.');
        } else {
            $this->warn('Exports folder does not exist, skipping...');
        }
    }

    /*
Clears Config Cache after updating env file
*/
    protected function clearCache()
    {
        $this->info('Clear Config Cache');
        $this->call('config:clear');
    }

    /**
     * Run all the seeders.
     *
     * @return void
     */
    protected function seedDatabase()
    {
        $this->line('Seeding Database ==');

        # List of modules to seed
        $modules = ['Leads', 'Clients', 'RolePermission',];

        foreach ($modules as $module) {
            if (Module::has($module)) {
                $this->info("Seeding module: $module");
                $this->call("module:seed", ['module' => $module]);
            } else {
                $this->warn("Module '$module' not found, skipping seeder...");
            }
        }

        $this->call('db:seed');

        $this->info('Database Seeding successful');
    }

    /**
     * Generate private application key.
     *
     * @return void
     */
    protected function generateAppKey()
    {
        $this->line('Generating application key..');

        $this->call('key:generate', ['--force' => true]);

        $this->info('Application key generated successfully.');
    }

    /**
     * Setup the database.
     *
     * @return void
     */
    protected function setupDatabase($type)
    {
        $this->line('Received Type ' . $type);
        $this->line('Installing the database...');
        if ($type == "fresh") {
            $this->info('Running Fresh Database Migration');
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->info('Running  Database Refresh');
            $this->call('migrate:refresh');
        }
        $this->info('Database installed successfully.');
    }

    /**
     * Create Storage Link.
     *
     * @return void
     */
    protected function storageLink()
    {
        $this->call('storage:link');
        $this->line(' Storage Linked Successfully...');
    }

    protected function envUpdateCmd()
    {
        $this->call('env:up');
        $this->line('Env Up date Successfully...');
    }

    protected function setupCountry()
    {
        $this->call('import:country');
        $this->call('import:state');
        $this->call('import:city');
    }
}
