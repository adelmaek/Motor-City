<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->double("value",40,3);
            $table->date('date');
            $table->bigInteger('accountId');
            $table->bigInteger('brandId');
            $table->string('type');
            $table->string('fromBankName')->nullable();
            $table->string('checkNumber')->nullable();
            $table->date('checkValidityDate')->nullable();
            $table->bigInteger('checKToBankId')->nullable();
            $table->date('checkSettlingDate')->nullable();
            $table->boolean('settled')->nullable();
            $table->boolean('confirmSettling')->nullable();
            $table->bigInteger('userId');
            $table->string('description');
            $table->string('clientName');
            $table->double("currentBalance",40,3);
            $table->boolean('automated')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
