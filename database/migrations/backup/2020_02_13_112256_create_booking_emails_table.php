<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('username',100)->nullable();
            $table->string('hour',50)->nullable();
            $table->integer('is_read')->unsigned()->nullable();
            $table->date('is_read_date')->nullable();
            $table->string('action',100);
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
        Schema::dropIfExists('booking_emails');
    }
}
