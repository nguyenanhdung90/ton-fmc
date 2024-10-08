<?php

namespace App\Tons\Transactions;

interface CollectAttributeInterface
{
    public function collect(array $data): array;
}
