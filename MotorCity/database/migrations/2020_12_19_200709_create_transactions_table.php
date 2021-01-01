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
            $table->bigInteger('fromBankId')->nullable();
            $table->bigInteger('toBankId')->nullable();
            $table->bigInteger('userId');
            $table->string('description');
            $table->string('clientName');
            $table->double("currentBalance",40,3);
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
