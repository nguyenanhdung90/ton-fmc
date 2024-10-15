<?php

namespace App\Tons\Transactions;

use App\Tons\HttpClient\TonCenterV3Client;
use App\Tons\Jettons\JettonWallet;
use Illuminate\Support\Arr;

class CollectJettonWalletAttribute extends CollectAttribute
{
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $source = Arr::get($data, 'in_msg.source');
        $httpClientV3 = new TonCenterV3Client();
        $jettonWallet = $httpClientV3->getJettonWallet(['address' => $source, 'limit' => 1, 'offset' => 0]);
        $trans['jetton_wallet'] = $jettonWallet instanceof JettonWallet ? $jettonWallet->getFriendlyMasterJetton() :
            null;
        return array_merge($parentTrans, $trans);
    }
}
