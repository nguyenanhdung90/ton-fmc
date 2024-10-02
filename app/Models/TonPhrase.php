<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TonPhrase extends Model
{
    use HasFactory;

    protected $table = 'ton_phrases';

    protected $fillable = [
        'word', 'order', 'ton_wallet_id'
    ];
}
