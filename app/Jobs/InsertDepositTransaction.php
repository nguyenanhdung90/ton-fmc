<?php

namespace App\Jobs;

use App\Models\WalletTonTransaction;
use App\Tons\Transactions\CollectHashLtTotalFeesaAttribute;
use App\Tons\Transactions\CollectTransactionAttribute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ClientTrait;

class InsertDepositTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ClientTrait;

    private array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $collectTransactionAttribute = new CollectTransactionAttribute();
        $hash = new CollectHashLtTotalFeesaAttribute($collectTransactionAttribute);
        $trans = $hash->collect($this->data);
        Log::info($trans);
        return;







        Log::info($this->data);
        DB::beginTransaction();
        try {
            $hash = Arr::get($this->data, 'hash');
            $countTransaction = WalletTonTransaction::where('hash', $hash)->count();
            if ($countTransaction) {
                return;
            }
            if (Arr::get($this->data, 'in_msg.msg_type') !== 'int_msg') {
                // this is not received transaction
                return;
            }
            $lt = $this->data['lt'];
            $totalFees = $this->data['total_fees'];

            $currency = $memo = $source = '';
            $amount = 0;
            if ($this->data['in_msg']['decoded_op_name'] === 'jetton_notify') {
                //$currency = Arr::get($this->data, 'in_msg.decoded_body.text'); // get api for currency
                $memo = Arr::get($this->data, 'in_msg.decoded_body.forward_payload.value.value.text');
                $source = Arr::get($this->data, 'in_msg.decoded_body.sender');
                $amount = Arr::get($this->data, 'in_msg.decoded_body.amount');
            } elseif ($this->data['in_msg']['decoded_op_name'] === 'text_comment') {
                $memo = Arr::get($this->data, 'in_msg.decoded_body.text');
                $currency = "TON";
                $source = Arr::get($this->data, 'in_msg.source.address');
                $amount = Arr::get($this->data, 'in_msg.value');
            } else {
                return;
            }


            $walletTonTransaction = new WalletTonTransaction;
            $walletTonTransaction->from_address_wallet = $source;
            $walletTonTransaction->type = 'DEPOSIT';
            $walletTonTransaction->currency = $currency;
            $walletTonTransaction->to_memo = $memo;
            $walletTonTransaction->hash = $hash;
            $walletTonTransaction->lt = $lt;
            $walletTonTransaction->amount = $amount;
            $walletTonTransaction->total_fee = $totalFees;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage() . "\n";
        }
    }

    private function getJettonsByEvent(string $event)
    {

    }
}
