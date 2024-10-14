<?php

namespace App\Tons\Jettons;

use Illuminate\Support\Arr;

class JettonWallet
{
    private string $rawAddress;

    private string $friendlyAddress;

    private int $balance;

    private string $rawOwner;

    private string $friendlyOwner;

    private string $rawMasterJetton;

    private string $friendlyMasterJetton;

    public static function fromResponseV3JettonWallet(array $jettonWallets, array $addressBooks): static
    {
        $jettonWallet = new static();
        $jettonWallet->rawAddress = Arr::get($jettonWallets, 'address');
        $jettonWallet->friendlyAddress = Arr::get($addressBooks, $jettonWallet->rawAddress . '.user_friendly');
        $jettonWallet->rawOwner = Arr::get($jettonWallets, 'owner');
        $jettonWallet->friendlyOwner = Arr::get($addressBooks, $jettonWallet->rawOwner . '.user_friendly');
        $jettonWallet->rawMasterJetton = Arr::get($jettonWallets, 'jetton');
        $jettonWallet->friendlyMasterJetton = Arr::get($addressBooks, $jettonWallet->rawMasterJetton . '.user_friendly');
        $jettonWallet->balance = Arr::get($jettonWallets, 'balance');
        return $jettonWallet;
    }

    public function getFriendlyAddress(): string
    {
        return $this->friendlyAddress;
    }

    public function getFriendlyMasterJetton(): string
    {
        return $this->friendlyMasterJetton;
    }
}
