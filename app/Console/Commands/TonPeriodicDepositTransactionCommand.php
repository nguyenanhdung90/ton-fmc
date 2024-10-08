<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TonPeriodicDepositTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ton:periodic_deposit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get new transactions from wallet';

    /**
     * @var string
     */
    private string $baseUri;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while (true) {
            echo "get new transactions ... \n";
            sleep(1);

        }
        return Command::SUCCESS;
    }
}
