<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_ton_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('from_address_wallet')->nullable();
            $table->unsignedInteger('from_memo')->nullable();
            $table->enum('type', ['DEPOSIT', 'WITHDRAW']);
            $table->unsignedInteger('to_memo');
            $table->string('hash');
            $table->unsignedDecimal('amount', 20, 9)->default(0);
            $table->enum('currency', ['TON', 'USDT']);
            $table->unsignedDecimal('total_fee', 20, 9)->default(0)->comment('currency = TON');
            $table->unsignedBigInteger('lt');
            $table->foreign('from_memo')
                ->references('memo')
                ->on('wallet_ton_memos');
            $table->foreign('to_memo')
                ->references('memo')
                ->on('wallet_ton_memos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_ton_transactions');
    }
};
