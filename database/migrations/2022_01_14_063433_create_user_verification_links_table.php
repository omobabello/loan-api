<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserVerificationLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_verification_links', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('verification_hash'); 
            $table->dateTime('expiry');
            $table->boolean('status');
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
        Schema::dropIfExists('user_verification_links', function(Blueprint $table){
            $table->dropForeign(['user_id']);
        });
    }
}
