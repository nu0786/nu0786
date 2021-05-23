<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanPaymentsHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_payments_histories', function (Blueprint $table) {
            $table->increments('loan_payments_histories_id');
            $table->integer('users_id')->index();
            $table->integer('loans_id')->index();
            $table->decimal('amount_paid',10,2)->nullable();
            $table->date('paid_at')->nullable();
            $table->enum('payment_status', ['success','rejected','failed'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan_payments_histories');
    }
}
