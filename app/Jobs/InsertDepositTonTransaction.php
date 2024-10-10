<?php

namespace App\Jobs;

use App\Tons\Transactions\CollectCurrencyTypeTonAttribute;
use App\Tons\Transactions\CollectHashLtTotalFeesTonAttribute;
use App\Tons\Transactions\CollectMemoSenderAmountTonAttribute;
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
use App\Tons\Transactions\TransactionHelper;

class InsertDepositTonTransaction implements ShouldQueue
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
            if (is_null(Arr::get($this->data, 'description.action'))) {
                // this is not action
                return;
            }
            if (count(Arr::get($this->data, 'out_msgs'))) {
                // this is not received transaction
                return;
            }
            if (Arr::get($this->data, 'in_msg.source') === Arr::get($this->data, 'in_msg.destination')) {
                // this is not from other wallet
                return;
            }
            $hash = TransactionHelper::toHash(Arr::get($this->data, 'hash'));
            $countTransaction = DB::table('wallet_ton_transactions')->where('hash', $hash)->count();
            if ($countTransaction) {
                return;
            }

            $collectTransactionAttribute = new CollectTransactionAttribute();
            $hashLtFees = new CollectHashLtTotalFeesTonAttribute($collectTransactionAttribute);
            $memoSenderAmount = new CollectMemoSenderAmountTonAttribute($hashLtFees);
            $currencyType = new CollectCurrencyTypeTonAttribute($memoSenderAmount);
            $trans = $currencyType->collect($this->data);

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
