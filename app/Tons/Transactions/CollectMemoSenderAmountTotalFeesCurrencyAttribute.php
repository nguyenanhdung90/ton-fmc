<?php

namespace App\Tons\Transactions;

use App\Exceptions\InvalidJettonException;
use App\Exceptions\InvalidJettonMasterException;
use App\Tons\HttpClient\TonCenterV3Client;
use App\Tons\Jettons\JettonMaster;
use App\Tons\Jettons\JettonWallet;
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
        $source = Arr::get($data, 'in_msg.source');
        $httpClientV3 = new TonCenterV3Client();
        $jettonWallet = $httpClientV3->getJettonWallet(['address' => $source, 'limit' => 1, 'offset' => 0]);
        if ($jettonWallet instanceof JettonWallet) {
            $jettonMaster = $httpClientV3->getJettonMaster([
                'address' => $jettonWallet->getFriendlyMasterJetton(), 'limit' => 1, 'offset' => 0]);
            if (!$jettonMaster instanceof JettonMaster) {
                throw new InvalidJettonMasterException("Invalid Jetton master.");
            }
            $currency = $jettonMaster->getSymbol();
            if (!in_array($currency, config('services.ton.valid_currencies'))) {
                throw new InvalidJettonMasterException("No support  jetton " . $currency);
            }
            $trans['currency'] = $currency;
            $totalFess = (int)Arr::get($data, 'total_fees') / pow(10, (int)$jettonMaster->getDecimals());
            $trans['total_fees'] = $totalFess;
            $bodyTrans = $this->parseJettonBody(Arr::get($data, 'in_msg.message_content.body'));
            $trans['amount'] = $bodyTrans['amount'] / pow(10, (int)$jettonMaster->getDecimals());
            $trans['from_address_wallet'] = $bodyTrans['from_address_wallet'];
            $trans['to_memo'] = $bodyTrans['comment'];
            return array_merge($parentTrans, $trans);
        }

        $tonTrans = $this->getTonTran($data);
        return array_merge($parentTrans, $tonTrans);
    }


    private function getTonTran($data): array
    {
        $source = Arr::get($data, 'in_msg.source');
        $trans['currency'] = config('services.ton.ton');
        $trans['to_memo'] = Arr::get($data, 'in_msg.message_content.decoded.comment');
        $address = new Address($source);
        $trans['from_address_wallet'] = $address->asWallet(!config('services.tom.is_main'));
        $amount = (int)Arr::get($data, 'in_msg.value') / pow(10, TransactionHelper::TON_DECIMALS);
        $trans['amount'] = $amount;
        $totalFess = (int)Arr::get($data, 'total_fees') / pow(10, TransactionHelper::TON_DECIMALS);
        $trans['total_fees'] = $totalFess;
        return $trans;
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
            throw new InvalidJettonException("Invalid Jetton.");
        }
        $opcode = Bytes::bytesToHexString($slice->loadBits(32));
        if ($opcode != config('services.ton.jetton_opcode')) {
            throw new InvalidJettonException("Invalid Jetton notify");
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
