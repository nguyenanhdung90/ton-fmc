<?php

namespace App\Tons\HttpClient;

interface TonCenterV3ClientInterface
{
    public function getJettonWallet(array $params);
}
