<?php

namespace App\Tons\HttpClient;

use App\Tons\Jettons\JettonMaster;
use App\Tons\Jettons\JettonWallet;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TonCenterV3Client implements TonCenterV3ClientInterface
{
    /**
     * @var string
     */
    private string $baseUri;

    /**
     * @var string
     */
    private string $apiKey;

    public function __construct()
    {
        $this->baseUri = config('services.ton.is_main') ? config('services.ton.base_uri_ton_center_main') :
            config('services.ton.base_uri_ton_center_test');
        $this->apiKey = config('services.ton.is_main') ? config('services.ton.api_key_main') :
            config('services.ton.api_key_test');
    }

    public function getJettonWallet(array $params)
    {
        $response = $this->query($params, 'api/v3/jetton/wallets');
        if ($response->getStatusCode() != 200) {
            return;
        }
        $data = json_decode($response->getBody()->getContents(), true);
        if (empty($data['jetton_wallets'])) {
            return;
        }
        return JettonWallet::fromResponseV3JettonWallet($data['jetton_wallets'][0], Arr::get($data,
            'address_book'));
    }

    public function getJettonMaster(array $params)
    {
        $response = $this->query($params, 'api/v3/jetton/masters');
        if ($response->getStatusCode() != 200) {
            return;
        }
        $data = json_decode($response->getBody()->getContents(), true);
        if (empty($data['jetton_masters'])) {
            return;
        }
        return JettonMaster::fromResponseV3JettonMaster($data['jetton_masters'][0], Arr::get($data, 'address_book'));
    }

    public function getTransactionsBy(array $params): array
    {
        $response = $this->query($params, 'api/v3/transactions');
        if ($response->getStatusCode() != 200) {
            return [];
        }
        $data = json_decode($response->getBody()->getContents(), true);
        if (!Arr::has($data, 'transactions')) {
            return [];
        }
        return Arr::get($data, 'transactions');
    }

    private function query(array $params, string $path)
    {
        try {
            $client = new Client();
            $params['api_key'] = $this->apiKey;
            $query = http_build_query($params);
            $uri = $this->baseUri . $path . '?' . $query;
            return $client->request('GET', $uri);
        } catch (GuzzleException $e) {
            Log::error('Caught exception: ' . $e->getMessage());
            return;
        }
    }
}
