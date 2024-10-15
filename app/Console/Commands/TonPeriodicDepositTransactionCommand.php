<?php

namespace App\Console\Commands;

use App\Jobs\InsertDepositTonTransaction;
use App\Models\WalletTonTransaction;
use App\Tons\HttpClient\TonCenterV3Client;
use Illuminate\Console\Command;
use App\Traits\ClientTrait;

class TonPeriodicDepositTransactionCommand extends Command
{
    use ClientTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ton:periodic_deposit {--limit=100}';

    /**
     * Sync new transactions from root wallet.
     *
     * @var string
     */
    protected $description = 'Sync new transactions from root wallet';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $limit = $this->option('limit');
        $lastTransaction = WalletTonTransaction::whereNotNull('from_address_wallet')->orderBy('id', 'desc')->first();
        $startLt = $lastTransaction ? $lastTransaction->lt : 0;
        $params = ['limit' => $limit, 'sort_order' => 'asc', 'account' => config('services.ton.root_ton_wallet')];
        $httpClientV3 = new TonCenterV3Client();
        while (true) {
            printf("Get new transaction from lt: %s with limit: %s after 5s ... \n", $startLt, $limit);
            if ($startLt) {
                $params['start_lt'] = $startLt;
            }
            $transactions = $httpClientV3->getTransactionsBy($params);
            if (!count($transactions)) {
                echo "Have no transactions. \n";
            } else {
                foreach ($transactions as $transaction) {
                    InsertDepositTonTransaction::dispatch($transaction);
                }
                $lastTransaction = end($transactions);
                $startLt = $lastTransaction['lt'];
            }
            sleep(5);
        }
        return Command::SUCCESS;
    }
}
