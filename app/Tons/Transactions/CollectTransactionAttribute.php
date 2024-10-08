<?php

namespace App\Tons\Transactions;

class CollectTransactionAttribute implements CollectAttributeInterface
{
    public function collect(array $data): array
    {
        return [
            'hash' => null,
            'lt' => null,
            'total_fees' => null,
            'to_memo' => null,
            'from_address_wallet' => null,
            'amount' => null,
        ];
    }
}
