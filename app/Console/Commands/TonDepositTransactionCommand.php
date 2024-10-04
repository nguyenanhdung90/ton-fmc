<?php

namespace App\Console\Commands;

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
     * Track TON periodic transaction deposit of root wallet.
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
        //sleep(1);
        $from = floor(microtime(true) * 10000);
        echo "from: " . $from . "\n";
        $to = floor(microtime(true) * 10000);
        echo "to  : " . $to . "\n";
        $param = [
            'address' => config('services.ton.root_ton_wallet'),
            'limit' => 100,
//            'to_lt' => 0,
            'archival' => false,
        ];
        $query = http_build_query($param);
        $baseUri = config('services.ton.is_main') ? config('services.ton.base_uri_main') :
            config('services.ton.base_uri_test');
        $uri = "$baseUri/getTransactions?$query";
        //echo "\n";
        //echo $uri;
        $data = $this->get($uri);


        $content = (json_decode($data['content'], true));
        Log::info('lolo: ' . $data['content']);
//        echo $data['content'];
//        echo gettype(json_decode($data['content'], true));
        //Log::info('handle: ' . getType($data['content'], true));
        return Command::SUCCESS;
    }
}
