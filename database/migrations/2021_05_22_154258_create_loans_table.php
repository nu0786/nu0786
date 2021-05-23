<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('loans_id');
            $table->integer('users_id')->index();
            $table->decimal('total_amount',10,2)->nullable();
            $table->decimal('total_amount_due',10,2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('last_send_at')->nullable();
            $table->date('last_paid_at')->nullable();
            $table->enum('status', ['approved','cancelled','pending'])->nullable();
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
        Schema::drop('loans');
    }
}
