<?php

namespace App\Tons\Withdraws;

use App\Exceptions\InvalidWithdrawMemoToMemoException;
use App\Models\WalletTonMemo;
use App\Tons\Transactions\TransactionHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WithdrawMemoToMemo implements WithdrawMemoToMemoInterface
{
    /**
     * @throws InvalidWithdrawMemoToMemoException
     */
    public function transfer(string $fromMemo, string $toMemo, float $amount, string $currency)
    {
        $sourceWalletTonMemo = WalletTonMemo::where('memo', $fromMemo)
            ->where('currency', $currency)->lockForUpdate()
            ->first();
        if (!$sourceWalletTonMemo) {
            throw new InvalidWithdrawMemoToMemoException('None exist source memo',
                InvalidWithdrawMemoToMemoException::NONE_EXIST_SOURCE_MEMO);
        }
        $amountToken = TransactionHelper::toToken($amount, $currency);
        if ($amountToken == 0) {
            throw new InvalidWithdrawMemoToMemoException('Amount is not zero',
                InvalidWithdrawMemoToMemoException::INVALID_AMOUNT);
        }
        if ($amountToken > $sourceWalletTonMemo->amount) {
            throw new InvalidWithdrawMemoToMemoException('Amount is not enough',
                InvalidWithdrawMemoToMemoException::AMOUNT_SOURCE_MEMO_NOT_ENOUGH);
        }
        $updateSourceAmount = TransactionHelper::toToken($sourceWalletTonMemo->amount, $currency) - $amountToken;
        $destinationWalletTonMemo = WalletTonMemo::where('memo', $toMemo)
            ->where('currency', $currency)->lockForUpdate()
            ->first();
        if (!$destinationWalletTonMemo) {
            throw new InvalidWithdrawMemoToMemoException('None exist destination memo',
                InvalidWithdrawMemoToMemoException::NONE_EXIST_DESTINATION_MEMO);
        }
        $updateDestinationAmount = TransactionHelper::toToken($destinationWalletTonMemo->amount, $currency) +
            $amountToken;
        DB::transaction(function () use (
            $fromMemo, $toMemo, $amountToken, $currency, $updateSourceAmount,
            $updateDestinationAmount
        ) {
            DB::table('wallet_ton_memos')->where('memo', $fromMemo)->where('currency', $currency)
                ->update(['amount' => $updateSourceAmount]);
            DB::table('wallet_ton_memos')->where('memo', $toMemo)->where('currency', $currency)
                ->update(['amount' => $updateDestinationAmount]);
            DB::table('wallet_ton_transactions')->insert([
                'from_address_wallet' => null,
                'from_memo' => $fromMemo,
                'type' => config('services.ton.deposit'),
                'to_memo' => $toMemo,
                'amount' => $amountToken,
                'hash' => TransactionHelper::uniqueTransactionHash(),
                'currency' => $currency,
                'total_fees' => 0,
                'lt' => Carbon::now()->timestamp,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
            $tranId = DB::getPdo()->lastInsertId();
            DB::table('wallet_ton_deposits')->insert([
                "memo" => $toMemo,
                "currency" => $currency,
                "amount" => $amountToken,
                "transaction_id" => $tranId,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
        }, 5);
    }
}
