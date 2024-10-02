<?php

namespace App\Tons\Phrases;


use App\Models\TonPhrase;

class Phrase implements PhraseInterface
{
    public function getPhrasesBy(int $walletId): array
    {
        return TonPhrase::where('ton_wallet_id', $walletId)->orderBy('order', 'ASC')->pluck("word")->toArray();
    }
}
