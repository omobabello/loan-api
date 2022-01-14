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
            $table->uuid('user_id'); 
            $table->string('verification_hash'); 
            $table->boolean('status');
            $table->foreignUuid('id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropForeign(['user_id']);
        Schema::dropIfExists('user_verification_links');
    }
}
