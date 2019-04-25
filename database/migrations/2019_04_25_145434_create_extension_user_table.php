<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtensionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('extension_id');
            $table->unsignedbigInteger('user_id');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('extension_id')->references('id')->on('extensions');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extension_user');
    }
}
