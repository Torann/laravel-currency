<?php

namespace Torann\Currency\Console;

use Illuminate\Console\Command;
use Torann\Currency\SourceManager;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update {--s|source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update exchange rates from the source';

    /**
     * The source manager instance.
     *
     * @var \Torann\Currency\SourceManager
     */
    protected $manager;

    /**
     * Create a new command instance.
     *
     * @param  \Torann\Currency\SourceManager  $manager
     * @return void
     */
    public function __construct(SourceManager $manager)
    {
        $this->manager = $manager;

        parent::__construct();
    }

    /**
     * Execute the console command for Laravel 5.4 and below
     *
     * @return void
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $source = $this->option('source') ?: config('currency.source');

        $this->info('Updating currency exchange rates...');

        try {
            $this->manager->fetchFrom($source)->update();

            $this->info('Exchange rates were successfully updated.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
