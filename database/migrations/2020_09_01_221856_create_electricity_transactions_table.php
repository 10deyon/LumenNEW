<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectricityTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::defaultStringLength(191);
        Schema::create('electricity_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->string('meterType');
            $table->string('meter_number')->nullable();
            $table->string('customer_address')->nullable();
            $table->integer('amount')->nullable();
            $table->string('status')->default('Not Completed');
            $table->string('phone')->nullable();
            // $table->string('request_id')->nullable();
            $table->string('token')->nullable();
            $table->string('bonus_token')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_date')->nullable();
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
        Schema::dropIfExists('electricity_transactions');
    }
}
