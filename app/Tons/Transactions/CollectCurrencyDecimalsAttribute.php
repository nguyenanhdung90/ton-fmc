<?php

namespace App\Tons\Transactions;

use Illuminate\Support\Arr;
use App\Traits\ClientTrait;
use Illuminate\Support\Facades\Log;

class CollectCurrencyDecimalsAttribute extends CollectAttribute
{
    use ClientTrait;

    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $decodedOpName = Arr::get($data, 'in_msg.decoded_op_name');
        if ($decodedOpName === config('services.ton.text_comment')) {
            $trans = [
                'currency' => config('services.ton.ton'),
                'decimals' => 9,
            ];
            return array_merge($parentTrans, $trans);
        }
        if ($decodedOpName === config('services.ton.jetton_notify')) {
            $hash = Arr::get($data, 'hash');
            $baseUri = config('services.ton.is_main') ? config('services.ton.base_uri_ton_api_main') :
                config('services.ton.base_uri_ton_api_test');
            $uri = $baseUri . "v2/events/" . $hash . "/jettons";
            $results = $this->httpGet($uri);
            if ($results['status'] != 200) {
                return $parentTrans;
            }
            $contents = json_decode($results['content'], true);
            $trans = [
                'currency' => Arr::get($contents, 'actions.0.JettonTransfer.jetton.symbol'),
                'decimals' => Arr::get($contents, 'actions.0.JettonTransfer.jetton.decimals'),
            ];
            return array_merge($parentTrans, $trans);
        }
    }
}
