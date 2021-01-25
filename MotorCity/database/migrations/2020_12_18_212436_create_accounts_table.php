<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name");
            $table->double("balance",40,3);
            $table->double("initialBalance",40,3);
            $table->string('type');
            $table->bigInteger('bankID')->nullable();
            $table->bigInteger('bankAccountId')->nullable();
            $table->bigInteger('brandID')->nullable(); //bank accounts are brandless
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
