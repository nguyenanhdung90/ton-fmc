<?php

namespace App\Jobs;

use App\Models\WalletTonTransaction;
use App\Tons\Transactions\CollectCurrencyDecimalsAttribute;
use App\Tons\Transactions\CollectHashLtTotalFeesAttribute;
use App\Tons\Transactions\CollectMemoSenderAmountAttribute;
use App\Tons\Transactions\CollectTransactionAttribute;
use Carbon\Carbon;
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
            $decodedOpName = Arr::get($this->data, 'in_msg.decoded_op_name');
            if (!in_array($decodedOpName, ['jetton_notify', 'text_comment'])) {
                return;
            }

            $collectTransactionAttribute = new CollectTransactionAttribute();
            $hashLtFees = new CollectHashLtTotalFeesAttribute($collectTransactionAttribute);
            $memoSenderAmount = new CollectMemoSenderAmountAttribute($hashLtFees);
            $currencyDecimal = new CollectCurrencyDecimalsAttribute($memoSenderAmount);
            $trans = $currencyDecimal->collect($this->data);
            if (!in_array($trans['currency'], ['USDT', 'TON'])) {
                return;
            }

            $trans['type'] = config('services.ton.deposit');
            $trans['amount'] = $trans['amount'] / (pow(10, $trans['decimals']));
            $trans['total_fees'] = $trans['total_fees'] / (pow(10, $trans['decimals']));
            $trans['created_at'] = Carbon::now();
            $trans['updated_at'] = Carbon::now();
            unset($trans['decimals']);
            DB::transaction(function () use ($trans) {
                DB::table('wallet_ton_transactions')->insert($trans);
                $tranId = DB::getPdo()->lastInsertId();
                DB::table('wallet_ton_deposits')->insert([
                    "memo" => $trans['to_memo'],
                    "currency" => $trans['currency'],
                    "amount" => $trans['amount'],
                    "transaction_id" => $tranId,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
            }, 5);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
