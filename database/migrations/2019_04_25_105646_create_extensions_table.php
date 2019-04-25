<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extensions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('number')->unique();
            $table->string('name');
            $table->string('message')->default('Message not setup yet');
            $table->string('transfer_prompt')->default('Transfer prompt not setup yet');
            $table->string('voicemail_prompt')->default('Voicemail prompt not setup yet');
            $table->timestamps();

            // Indexes
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extensions');
    }
}
