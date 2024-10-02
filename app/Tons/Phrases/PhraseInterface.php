<?php

namespace App\Tons\Phrases;

interface PhraseInterface
{
    public function getPhrasesBy(int $walletId): array;
}

