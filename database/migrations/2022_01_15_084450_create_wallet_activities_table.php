<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('wallet_id')->nullable()->constrained('wallets')->onUpdate('cascade')->nullOnDelete();
            $table->double('amount'); 
            $table->foreignUuid('from_wallet_id')->nullable()->constrained('wallets')->onUpdate('cascade')->nullOnDelete();
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
        Schema::dropIfExists('wallet_activities');
    }
}
