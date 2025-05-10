<?php

namespace App\Console\Commands;

 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class EnvUpdateCmd extends Command
{
    const GOOGLE_REDIRECT_URI = "";
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'env:up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Update ENV Value And Add ENV';

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
        $this->info('ENV Update Cmd Staring....');
 
        DotenvEditor::save();
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        $this->info('ENV Updated Successfully');
    }
}
