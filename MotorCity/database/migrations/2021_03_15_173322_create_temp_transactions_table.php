<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->double("value",40,3);
            $table->bigInteger('accountId');
            $table->string('comment');
            $table->boolean('confirmed');
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
        Schema::dropIfExists('temp_transactions');
    }
}
