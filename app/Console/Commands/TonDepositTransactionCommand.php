<?php

namespace App\Console\Commands;

use App\Jobs\InsertDepositTonTransaction;
use App\Tons\HttpClient\TonCenterV3Client;
use Illuminate\Console\Command;
use App\Traits\ClientTrait;


class TonDepositTransactionCommand extends Command
{
    const LIMIT = 10;

    use ClientTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ton:deposit';

    /**
     * get all transaction deposit of root wallet.
     *
     * @var string
     */
    protected $description = 'Track TON periodic transaction deposit of root wallet';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        echo "Start job \n";
        $params = [
            'limit' => self::LIMIT,
            'sort' => 'asc',
            'account' => config('services.ton.root_ton_wallet'),
        ];
        $httpClientV3 = new TonCenterV3Client();
        $offset = $i = 0;
        while (true) {
            $params['offset'] = $offset;
            $transactions = $httpClientV3->getTransactionsBy($params);
            if (empty($transactions)) {
                echo "Have no transactions. \n";
                break;
            }
            foreach ($transactions as $transaction) {
                InsertDepositTonTransaction::dispatch($transaction);
            }
            $offset = ($i + 1) * self::LIMIT;
            $i++;
        }
        echo "Finish job.";
        return Command::SUCCESS;
    }
}
