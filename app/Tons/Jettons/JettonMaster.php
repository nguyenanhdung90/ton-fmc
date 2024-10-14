<?php

namespace App\Tons\Jettons;

use Illuminate\Support\Arr;

class JettonMaster
{
    private string $rawAddress;

    private string $friendlyAddress;

    private string $decimals;

    private string $symbol;

    public static function fromResponseV3JettonMaster(array $jettonMasters, array $addressBooks): static
    {
        $jettonMaster = new static();
        $jettonMaster->rawAddress = Arr::get($jettonMasters, 'address');
        $jettonMaster->friendlyAddress = Arr::get($addressBooks, $jettonMaster->rawAddress . '.user_friendly');
        $jettonMaster->symbol = Arr::get($jettonMasters, 'jetton_content.symbol');
        $jettonMaster->decimals = Arr::get($jettonMasters, 'jetton_content.decimals');
        return $jettonMaster;
    }

    public function getDecimals(): string
    {
        return $this->decimals;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }
}
