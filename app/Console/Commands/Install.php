<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;

class Install extends Command
{
    protected $signature = 'module:install {db=refresh}';
    protected $description = 'Migrates Database, Seeds Database, Generates Keys, Sets Environment, Configures Reverb, and more.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $type = $this->argument('db');
        $acceptedArgs = ['fresh', 'refresh'];

        if (!in_array($type, $acceptedArgs)) {
            return $this->error('Invalid Argument: ' . $type);
        }

        $this->line('📦 Starting Project Installation with DB Mode: ' . strtoupper($type));

        # STEP 1: Generate Application Key
        $this->generateAppKey();

        # STEP 2: Optional - Clear Export Folder (if fresh)
        if ($type === "fresh") {
            $this->clearExportsFolder();
        }

        # STEP 3: Migrate Database
        $this->setupDatabase($type);

        # STEP 4: Seed the Database
        $this->seedDatabase();

        # STEP 5: Import Country, State, City
        # $this->setupCountry();

        # STEP 6: Create Storage Symlink
        $this->storageLink();

        # STEP 7: Clear Config Cache
        $this->clearCache();

        # STEP 8: Reverb Setup (Real-time Server)
        $this->reverbInstall();

        # STEP 9: Run env:up Command
        $this->envUpdateCmd();

        $this->info('🎉 Project Installation Complete!');
    }

    # 1. Generate Application Key
    protected function generateAppKey()
    {
        $this->line('🔐 Generating Application Key...');
        $this->call('key:generate', ['--force' => true]);
        $this->info('✅ App Key generated');
    }

    # 2. Optional - Clear Export Folder (if fresh)
    protected function clearExportsFolder()
    {
        $folders = [
            'exports' => 'Exports folder',
            'UserImages' => 'User Images folder',
            // 'client/Images' => 'Client Images folder',
        ];

        foreach ($folders as $folder => $label) {
            $path = storage_path("app/public/{$folder}");

            if (File::exists($path)) {
                File::cleanDirectory($path);
                $this->info("🧹 {$label} cleared successfully.");
            } else {
                $this->warn("⚠️ {$label} does not exist, skipping...");
            }
        }
    }

    # 3. Migrate Database
    protected function setupDatabase($type)
    {
        $this->line('🛠️ Running database migration: ' . strtoupper($type));

        if ($type === "fresh") {
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->call('migrate:refresh');
        }

        $this->info('✅ Database migration complete');
    }

    # 4: Seed the Database
    protected function seedDatabase()
    {
        $this->line('🌱 Seeding Database...');

        $modules = ['Leads', 'Clients', 'RolePermission'];
        foreach ($modules as $module) {
            if (Module::has($module)) {
                $this->info("🔧 Seeding module: $module");
                $this->call("module:seed", ['module' => $module]);
            } else {
                $this->warn("⚠️ Module '$module' not found, skipping...");
            }
        }

        $this->call('db:seed');
        $this->info('✅ Database Seeding completed');
    }

    /**
     * Imports Country, State, and City data using custom Artisan commands.
     *
     * This is useful to pre-fill geographical data for forms, user addresses, etc.
     * Make sure `import:country`, `import:state`, and `import:city` commands exist and are registered.
     *
     * @return void
     */
    protected function setupCountry()
    {
        $this->info('🌍 Importing Country, State, and City data...');

        $this->call('import:country');
        $this->call('import:state');
        $this->call('import:city');

        $this->info('✅ Country, State, and City data imported.');
    }

    # 6: Seed the Database
    protected function storageLink()
    {
        $this->call('storage:link');
        $this->info('🔗 Storage linked successfully');
    }

    # 7. Clear Config Cache
    protected function clearCache()
    {
        $this->info('🧼 Clearing config cache...');
        $this->call('config:clear');
        $this->call('config:cache');
        $this->call('optimize:clear');
    }

    # 8. Reverb Setup (Real-time Server)
    /**
     * Install and configure Laravel Reverb for real-time broadcasting.
     *
     * This method:
     * - Publishes Reverb config files (if not already published).
     * - Prompts the user to hit Enter at steps for better CLI experience.
     * - Installs Reverb setup.
     */
    protected function reverbInstall()
    {
        $this->info('⚡ Setting up Laravel Reverb...');

        // Prompt before publishing config
        $this->line('📄 Publishing Reverb config files...');
        $this->info('👉 Press Enter to continue...');
        fgets(STDIN);

        $this->callSilently('vendor:publish', [
            '--tag' => 'reverb-config',
            '--force' => true,
        ]);
        $this->info('✅ Reverb config published.');

        // Prompt before running install
        $this->line('🔧 Installing Reverb...');
        $this->info('👉 Press Double Enter to continue...');
        fgets(STDIN);

        $this->call('reverb:install');

        $this->info('✅ Reverb setup complete!');
    }


    # 9. Run env:up Command
    protected function envUpdateCmd()
    {
        $this->call('env:up');
        $this->info('🌍 .env updated successfully');
    }
}
