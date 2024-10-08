<?php

namespace App\Console\Commands;

use App\Jobs\InsertDepositTransaction;
use Illuminate\Console\Command;
use App\Traits\ClientTrait;
use Illuminate\Support\Facades\Log;


class TonDepositTransactionCommand extends Command
{
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
     * @var string
     */
    private string $baseUri;

    /**
     * TonDepositTransactionCommand constructor.
     */
    public function __construct()
    {
        $this->baseUri = config('services.ton.is_main') ? config('services.ton.base_uri_ton_api_main') :
            config('services.ton.base_uri_ton_api_test');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $params = ['limit' => 25];
        $beforeLt = 0;
        while (true) {
            if ($beforeLt) {
                $params['before_lt'] = $beforeLt;
            }
            $data = $this->getBatchBy($params);
            if (empty($data)) {
                // response error api ton
                break;
            }
            $transactions = $data['transactions'];
            if (empty($transactions)) {
                break;
            }
            $lastTransaction = end($transactions);
            $beforeLt = $lastTransaction['lt'];
            foreach ($transactions as $transaction) {
                InsertDepositTransaction::dispatch($transaction);
            }
        }
        echo "Finish job get all transaction";
        return Command::SUCCESS;
    }

    public function getBatchBy(array $params): array
    {
        $basePath = $this->baseUri . "v2/blockchain/accounts/" . config('services.ton.root_ton_wallet') .
            "/transactions";
        $query = http_build_query($params);
        $uri = $basePath . '?' . $query;
        $results = $this->httpGet($uri);
        if ($results['status'] != 200) {
            return [];
        }
        return json_decode($results['content'], true);
    }
}
