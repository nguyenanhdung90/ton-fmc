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
        Schema::create('ton_phrases', function (Blueprint $table) {
            $table->id();
            $table->string('word', 100);
            $table->unsignedInteger('order');
            $table->unsignedBigInteger('ton_wallet_id');
            $table->foreign('ton_wallet_id')
                ->references('id')
                ->on('ton_wallets');
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
        Schema::dropIfExists('ton_phrases');
    }
};
