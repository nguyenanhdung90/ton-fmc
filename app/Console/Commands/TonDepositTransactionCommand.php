<?php

namespace App\Console\Commands;

use App\Jobs\InsertDepositTransaction;
use Illuminate\Console\Command;
use App\Traits\ClientTrait;
use Illuminate\Support\Facades\Log;


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
     * @var string
     */
    private string $baseUri;

    /**
     * @var string
     */
    private string $apiKey;

    /**
     * TonDepositTransactionCommand constructor.
     */
    public function __construct()
    {
        $this->baseUri = config('services.ton.is_main') ? config('services.ton.base_uri_ton_center_main') :
            config('services.ton.base_uri_ton_center_test');
        $this->apiKey =  config('services.ton.is_main') ? config('services.ton.api_key_main') :
            config('services.ton.api_key_test');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->syncTonDeposit();
        echo "Finish job.";
        return Command::SUCCESS;
    }

    private function syncTonDeposit() {
        $params = [
            'limit' => self::LIMIT,
            'sort' => 'asc',
            'account' => config('services.ton.root_ton_wallet'),
            'api_key' => $this->apiKey,
        ];
        $offset = $i = 0;
        while (true) {
            $params['offset'] = $offset;
            $data = $this->getTonBatchBy($params);
            if (empty($data)) {
                // response error api ton
                break;
            }
            $transactions = $data['transactions'];
            if (empty($transactions)) {
                break;
            }
            foreach ($transactions as $transaction) {
                InsertDepositTransaction::dispatch($transaction);
            }
            $offset = ($i + 1) * self::LIMIT;
            $i++;
        }
    }

    private function getTonBatchBy(array $params): array
    {
        $basePath = $this->baseUri . "api/v3/transactions";
        $query = http_build_query($params);
        $uri = $basePath . '?' . $query;
        $results = $this->httpGet($uri);
        if ($results['status'] != 200) {
            return [];
        }
        return json_decode($results['content'], true);
    }
}
