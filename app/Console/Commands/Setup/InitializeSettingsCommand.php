<?php

namespace App\Console\Commands\Setup;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('migrate:settings')]
#[Description('Generate the settings table and seed it with default values.')]
class InitializeSettingsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
