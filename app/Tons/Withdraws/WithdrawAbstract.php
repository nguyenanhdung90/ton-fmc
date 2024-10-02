<?php

namespace App\Tons\Withdraws;

use App\Models\TonPhrase;
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Olifanton\Ton\Transports\Toncenter\ClientOptions;
use Olifanton\Ton\Transports\Toncenter\ToncenterHttpV2Client;
use Olifanton\Ton\Transports\Toncenter\ToncenterTransport;

abstract class WithdrawAbstract
{
    abstract public function getWallet($pubicKey);

    protected function getBaseUri()
    {
        return config('services.ton.is_main') ? config('services.ton.base_uri_main') :
            config('services.ton.base_uri_test');
    }

    protected function getTonApiKey()
    {
        return config('services.ton.is_main') ? config('services.ton.api_key_main') :
            config('services.ton.api_key_test');
    }

    protected function getTransport(): ToncenterTransport
    {
        $httpClient = new HttpMethodsClient(
            Psr18ClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
        );
        $tonCenter = new ToncenterHttpV2Client(
            $httpClient,
            new ClientOptions(
                $this->getBaseUri(),
                $this->getTonApiKey()
            )
        );
        return new ToncenterTransport($tonCenter);
    }
}
