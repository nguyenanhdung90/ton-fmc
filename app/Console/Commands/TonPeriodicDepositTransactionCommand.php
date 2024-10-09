<?php

namespace App\Console\Commands;

use App\Models\WalletTonTransaction;
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
    public function handle()
    {
        $params = ['limit' => 25, 'sort_order' => 'ASC'];
        $afterLt = 0;
        while (true) {
            echo "get new transactions ... \n";
            $theLastWalletTransaction = WalletTonTransaction::orderBy('lt', "DESC")->first();
            if (!$theLastWalletTransaction) {
                $afterLt = $theLastWalletTransaction->lt;
            }
            if ($afterLt) {
                $params['after_lt'] = $afterLt;
            }
            $basePath = $this->baseUri . "v2/blockchain/accounts/" . config('services.ton.root_ton_wallet') .
                "/transactions";
            $query = http_build_query($params);
            $results = $this->httpGet($basePath . '?' . $query);
            if ($results['status'] != 200) {
                continue;
            }
            sleep(1);
        }
        return Command::SUCCESS;
    }
}
