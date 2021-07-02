<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->enum('ask_for_transfer', [0, 1])->nullable();
            $table->string('reponsible_person_ti')->nullable();
            $table->date('last_date_ti')->nullable();
            $table->text('transfer_detail')->nullable();
            $table->enum('transfer_organize', [0, 1])->nullable();
            $table->string('reponsible_person_to')->nullable();
            $table->date('last_date_to')->nullable();
            $table->text('transfer_organize_detail')->nullable();
            $table->enum('itinerary_finalised', [0, 1])->nullable();
            $table->string('reponsible_person_if')->nullable();
            $table->date('last_date_if')->nullable();
            $table->text('itinerary_finalised_detail')->nullable();
            $table->date('itinerary_finalised_date')->nullable();
            $table->enum('travel_document_prepared', [0, 1])->nullable();
            $table->string('reponsible_person_tdp')->nullable();
            $table->date('last_date_tdp')->nullable();
            $table->date('travel_document_prepared_date')->nullable();
            $table->enum('travel_document_sent', [0, 1])->nullable();
            $table->string('reponsible_person_tds')->nullable();
            $table->date('last_date_tds')->nullable();
            $table->text('travel_document_sent_detail')->nullable();
            $table->date('travel_document_sent_date')->nullable();
            $table->enum('app_login_sent', [0, 1])->nullable();
            $table->string('reponsible_person_als')->nullable();
            $table->date('last_date_als')->nullable();
            $table->text('app_login_sent_detail')->nullable();            
            $table->timestamps();
            
            $table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_data');
    }
}
