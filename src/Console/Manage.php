<?php

namespace Torann\Currency\Console;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;

class Manage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:manage
                                {action : Action to perform (add, update, or delete)}
                                {currency : Code or comma separated list of codes for currencies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage currency values';

    /**
     * Currency storage instance
     *
     * @var \Torann\Currency\Contracts\DriverInterface
     */
    protected $storage;

    /**
     * All installable currencies.
     *
     * @var array
     */
    protected $currencies;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->storage = app('currency')->getDriver();
        $this->currencies = include(__DIR__ . '/../../resources/currencies.php');

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
        $action = $this->getActionArgument(['add', 'update', 'delete']);

        foreach ($this->getCurrencyArgument() as $currency) {
            $this->$action(strtoupper($currency));
        }
    }

    /**
     * Add currency to storage.
     *
     * @param string $currency
     *
     * @return void
     */
    protected function add($currency)
    {
        if (($data = $this->getCurrency($currency)) === null) {
            $this->error("Currency \"{$currency}\" not found");
            return;
        }

        $this->output->write("Adding {$currency} currency...");

        $data['code'] = $currency;

        if (is_string($result = $this->storage->create($data))) {
            $this->output->writeln('<error>' . ($result ?: 'Failed') . '</error>');
        } else {
            $this->output->writeln("<info>success</info>");
        }
    }

    /**
     * Update currency in storage.
     *
     * @param string $currency
     *
     * @return void
     */
    protected function update($currency)
    {
        if (($data = $this->getCurrency($currency)) === null) {
            $this->error("Currency \"{$currency}\" not found");
            return;
        }

        $this->output->write("Updating {$currency} currency...");

        if (is_string($result = $this->storage->update($currency, $data))) {
            $this->output->writeln('<error>' . ($result ?: 'Failed') . '</error>');
        } else {
            $this->output->writeln("<info>success</info>");
        }
    }

    /**
     * Delete currency from storage.
     *
     * @param string $currency
     *
     * @return void
     */
    protected function delete($currency)
    {
        $this->output->write("Deleting {$currency} currency...");

        if (is_string($result = $this->storage->delete($currency))) {
            $this->output->writeln('<error>' . ($result ?: 'Failed') . '</error>');
        } else {
            $this->output->writeln("<info>success</info>");
        }
    }

    /**
     * Get currency argument.
     *
     * @return array
     */
    protected function getCurrencyArgument()
    {
        // Get the user entered value
        $value = preg_replace('/\s+/', '', $this->argument('currency'));

        // Return all currencies if requested
        if ($value === 'all') {
            return array_keys($this->currencies);
        }

        return explode(',', $value);
    }

    /**
     * Get action argument.
     *
     * @param array $validActions
     *
     * @return array
     */
    protected function getActionArgument($validActions = [])
    {
        $action = strtolower($this->argument('action'));

        if (in_array($action, $validActions) === false) {
            throw new \RuntimeException("The \"{$action}\" option does not exist.");
        }

        return $action;
    }

    /**
     * Get currency data.
     *
     * @param string $currency
     *
     * @return array
     */
    protected function getCurrency($currency)
    {
        return Arr::get($this->currencies, $currency);
    }
}
