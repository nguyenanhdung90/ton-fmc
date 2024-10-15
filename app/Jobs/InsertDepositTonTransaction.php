<?php

namespace App\Jobs;

use App\Tons\Transactions\CollectHashLtAttribute;
use App\Tons\Transactions\CollectJettonWalletAttribute;
use App\Tons\Transactions\CollectMemoSenderAmountTotalFeesCurrencyAttribute;
use App\Tons\Transactions\CollectTransactionDepositAttribute;
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
            if (count(Arr::get($this->data, 'out_msgs'))) {
                // this is not received transaction
                return;
            }
            $hash = TransactionHelper::toHash(Arr::get($this->data, 'hash'));
            $countTransaction = DB::table('wallet_ton_transactions')->where('hash', $hash)->count();
            if ($countTransaction) {
                return;
            }

            $collectTransaction = new CollectTransactionDepositAttribute();
            $collectHashLt = new CollectHashLtAttribute($collectTransaction);
            $collectJettonWallet = new CollectJettonWalletAttribute($collectHashLt);
            $collectMemoSenderAmountTotalFeesCurrency = new CollectMemoSenderAmountTotalFeesCurrencyAttribute
            ($collectJettonWallet);
            $trans = $collectMemoSenderAmountTotalFeesCurrency->collect($this->data);
            printf("inserting tran hash: %s currency: %s amount: %s \n", $trans['hash'], $trans['currency'],
                $trans['amount']);

            DB::transaction(function () use ($trans) {
                DB::table('wallet_ton_transactions')->insert($trans);
                $tranId = DB::getPdo()->lastInsertId();
                DB::table('wallet_ton_deposits')->insert([
                    "memo" => Arr::get($trans, 'to_memo'),
                    "currency" => Arr::get($trans, 'currency'),
                    "amount" => Arr::get($trans, 'amount'),
                    "transaction_id" => $tranId,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
                if (!empty($trans['to_memo'])) {
                    $walletMemo = DB::table('wallet_ton_memos')
                        ->where('memo', Arr::get($trans, 'to_memo'))
                        ->where('currency', Arr::get($trans, 'currency'))
                        ->lockForUpdate()
                        ->get(['id', 'memo', 'currency', 'amount'])
                        ->first();
                    if ($walletMemo) {
                        $updateAmount = $walletMemo->amount + $trans['amount'];
                        DB::table('wallet_ton_memos')->where('id', $walletMemo->id)
                            ->update(['amount' => $updateAmount]);
                    }
                }
            }, 5);
        } catch (\Exception $e) {
            Log::error("message: " . $this->data['hash'] . ' | ' . $e->getMessage());
        }
    }
}
