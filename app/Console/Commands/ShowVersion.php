<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class ShowVersion
 */
class ShowVersion extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Echoes the current version and some debug info.';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spectre:version';

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
    public function handle(): int
    {
        $this->line(sprintf('Firefly III Spectre importer v%s', config('spectre.version')));
        $this->line(sprintf('PHP: %s %s %s', PHP_SAPI, PHP_VERSION, PHP_OS));

        return 0;
    }
}
