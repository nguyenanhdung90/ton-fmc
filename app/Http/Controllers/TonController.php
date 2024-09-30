<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Olifanton\Ton\Contracts\Wallets\TransferOptions;
use Olifanton\Ton\Contracts\Wallets\V3\WalletV3Options;
use Olifanton\Ton\Transports\Toncenter\ToncenterHttpV2Client;
use Olifanton\Ton\Transports\Toncenter\ClientOptions;
use Olifanton\Ton\Transports\Toncenter\ToncenterTransport;
use Olifanton\Mnemonic\TonMnemonic;
use Olifanton\Ton\Contracts\Wallets\V3\WalletV3R2;
use Olifanton\Interop\Address;
use Olifanton\Interop\Units;
use Olifanton\Ton\Contracts\Wallets\Transfer;

class TonController extends Controller
{
    public function transfer(Request $request)
    {
        $isMainNet = false;
        $tonCenterApiKey = env("TON_API_KEY"); //0e7ac59d0a2c5142ecaeb08d0497efc3da085744c6382371ff5711e6cbac428f

        // HTTP client initialization
        $httpClient = new HttpMethodsClient(
            Psr18ClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
        );
        // Toncenter API client initialization
        $tonCenter = new ToncenterHttpV2Client(
            $httpClient,
            new ClientOptions(
                $isMainNet ? "https://toncenter.com/api/v2" : "https://testnet.toncenter.com/api/v2",
                $tonCenterApiKey
            )
        );
        $transport = new ToncenterTransport($tonCenter);
        $words = explode(" ", trim(env("TON_MNEMONIC"))); //
        $kp = TonMnemonic::mnemonicToKeyPair($words);
        $wallet = new WalletV3R2(new WalletV3Options($kp->publicKey));
        $extMsg = $wallet->createTransferMessage(
            [
                new Transfer(
                    new Address("0QB2qumdPNrPUzgAAuTvG43NNBg45Cl4Bi_Gt81vE-EwF70k"),
                    Units::toNano("0.003")
                )
            ],
            new TransferOptions((int)$wallet->seqno($transport))
        );
        $transport->sendMessage($extMsg, $kp->secretKey);
        return 'Success: ' . $wallet->getName();
    }
}
