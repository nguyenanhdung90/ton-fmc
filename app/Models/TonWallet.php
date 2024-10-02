<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TonWallet extends Model
{
    use HasFactory;

    protected $table = 'ton_wallets';

    protected $fillable = [
        'address', 'version', 'user_id'
    ];
}
