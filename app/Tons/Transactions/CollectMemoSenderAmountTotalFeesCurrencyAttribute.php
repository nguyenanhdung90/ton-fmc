<?php

namespace App\Tons\Transactions;

use App\Exceptions\InvalidJettonException;
use App\Exceptions\InvalidJettonMasterException;
use App\Tons\HttpClient\TonCenterV3Client;
use App\Tons\Jettons\JettonMaster;
use App\Traits\ClientTrait;
use Illuminate\Support\Arr;
use Olifanton\Interop\Address;
use Olifanton\Interop\Boc\Cell;
use Olifanton\Interop\Boc\Exceptions\CellException;
use Olifanton\Interop\Bytes;

class CollectMemoSenderAmountTotalFeesCurrencyAttribute extends CollectAttribute
{
    use ClientTrait;

    /**
     * @throws InvalidJettonMasterException
     * @throws CellException
     * @throws InvalidJettonException
     */
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $httpClientV3 = new TonCenterV3Client();
        $jettonWallet = Arr::get($parentTrans, 'jetton_wallet');
        $exceptParentTrans = Arr::except($parentTrans, ['jetton_wallet']);
        if ($jettonWallet) {
            $jettonMaster = $httpClientV3->getJettonMaster(['address' => $jettonWallet, 'limit' => 1, 'offset' => 0]);
            if (!$jettonMaster instanceof JettonMaster) {
                throw new InvalidJettonMasterException("Invalid Jetton master.",
                    InvalidJettonMasterException::INVALID_JETTON);
            }
            $currency = $jettonMaster->getSymbol();
            if (!in_array($currency, config('services.ton.valid_currencies'))) {
                throw new InvalidJettonMasterException("No support  jetton " . $currency,
                    InvalidJettonMasterException::NO_SUPPORT_JETTON);
            }
            Arr::set($trans, 'currency', $currency);
            $totalFess = (int)Arr::get($data, 'total_fees') / pow(10, (int)$jettonMaster->getDecimals());
            Arr::set($trans, 'total_fees', $totalFess);
            $bodyTrans = $this->parseJettonBody(Arr::get($data, 'in_msg.message_content.body'));
            $amount = Arr::get($bodyTrans, 'amount', 0) / pow(10, (int)$jettonMaster->getDecimals());
            Arr::set($trans, 'amount', $amount);
            $from_address_wallet = Arr::get($bodyTrans, 'from_address_wallet');
            Arr::set($trans, 'from_address_wallet', $from_address_wallet);
            $memo = Arr::get($bodyTrans, 'comment');
            Arr::set($trans, 'to_memo', $memo);
        } else {
            Arr::set($trans, 'currency', config('services.ton.ton'));
            $memo = Arr::get($data, 'in_msg.message_content.decoded.comment');
            Arr::set($trans, 'to_memo', $memo);
            $source = Arr::get($data, 'in_msg.source');
            $address = new Address($source);
            $fromAddressWallet = $address->asWallet(!config('services.tom.is_main'));
            Arr::set($trans, 'from_address_wallet', $fromAddressWallet);
            $amount = (int)Arr::get($data, 'in_msg.value') / pow(10, TransactionHelper::TON_DECIMALS);
            Arr::set($trans, 'amount', $amount);
            $totalFess = (int)Arr::get($data, 'total_fees') / pow(10, TransactionHelper::TON_DECIMALS);
            Arr::set($trans, 'total_fees', $totalFess);
        }
        return array_merge($exceptParentTrans, $trans);
    }

    /**
     * @throws CellException
     * @throws InvalidJettonException
     */
    private function parseJettonBody(string $body): array
    {
        $bytes = Bytes::base64ToBytes($body);
        $cell = Cell::oneFromBoc($bytes, true);
        $slice = $cell->beginParse();
        $remainBit = count($slice->getRemainingBits());
        if ($remainBit < 32) {
            throw new InvalidJettonException("Invalid Jetton.", InvalidJettonException::INVALID_JETTON);
        }
        $opcode = Bytes::bytesToHexString($slice->loadBits(32));
        if ($opcode != config('services.ton.jetton_opcode')) {
            throw new InvalidJettonException("Invalid Jetton notify",
                InvalidJettonException::INVALID_JETTON_OPCODE);
        }
        $slice->skipBits(64);
        $amount = $slice->loadCoins();
        $sender = $slice->loadAddress()->toString(true, true, null, true);
        $comment = null;
        if ($cellForward = $slice->loadMaybeRef()) {
            $forwardPayload = $cellForward->beginParse();
            $comment = $forwardPayload->loadString();
        }
        return [
            'amount' => (string)$amount,
            'from_address_wallet' => $sender,
            'comment' => $comment,
        ];
    }
}
