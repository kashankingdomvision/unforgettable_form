<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ref_no',50);
            $table->string('brand_name',50);
            $table->unsignedInteger('season_id');
            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
            $table->string('agency_booking',5);
            $table->string('pax_no',30);
            $table->date('date_of_travel');
            $table->string('flight_booked',5);
            $table->integer('fb_airline_name_id')->unsigned();
            $table->integer('fb_payment_method_id')->unsigned();
            $table->date('fb_booking_date');
            $table->string('fb_airline_ref_no',25);

            $table->date('fb_last_date')->nullable();
            $table->integer('fb_person')->unsigned()->nullable();
            $table->integer('fb_48hr')->unsigned()->nullable();
            $table->integer('fb_24hr')->unsigned()->nullable();
            $table->integer('fb_0hr')->unsigned()->nullable();
            $table->text('flight_booking_details')->nullable();
            $table->string('asked_for_transfer_details',5);
            $table->text('transfer_details')->nullable();
            $table->date('aft_last_date')->nullable();
            $table->integer('aft_person')->unsigned()->nullable();
            $table->integer('aft_48hr')->unsigned()->nullable();
            $table->integer('aft_24hr')->unsigned()->nullable();
            $table->integer('aft_0hr')->unsigned()->nullable();
            $table->date('form_sent_on');
            $table->date('form_received_on')->nullable();
            //
            $table->date('fso_last_date')->nullable();
            $table->integer('fso_person')->unsigned()->nullable();
            $table->integer('fso_48hr')->unsigned()->nullable();
            $table->integer('fso_24hr')->unsigned()->nullable();
            $table->integer('fso_0hr')->unsigned()->nullable();
            //
            $table->date('app_login_date')->nullable();
            $table->string('transfer_info_received',5)->nullable();
            $table->text('transfer_info_details')->nullable();
            // $table->date('not_received_yet');
            $table->string('itinerary_finalised',5);
            $table->text('itinerary_finalised_details')->nullable();
            $table->date('itf_last_date')->nullable();
            $table->date('itf_current_date')->nullable();
            $table->integer('itf_person')->unsigned()->nullable();
            $table->integer('itf_48hr')->unsigned()->nullable();
            $table->integer('itf_24hr')->unsigned()->nullable();
            $table->integer('itf_0hr')->unsigned()->nullable();

            $table->string('documents_sent',5);
            $table->text('documents_sent_details')->nullable();
            $table->date('tds_current_date')->nullable();
            
            $table->date('ds_last_date')->nullable();
            $table->integer('ds_person')->unsigned()->nullable();
            $table->integer('ds_48hr')->unsigned()->nullable();
            $table->integer('ds_24hr')->unsigned()->nullable();
            $table->integer('ds_0hr')->unsigned()->nullable();
            //
            $table->string('document_prepare',5);
            $table->date('dp_last_date')->nullable();
            $table->integer('dp_person')->unsigned()->nullable();
            $table->integer('dp_48hr')->unsigned()->nullable();
            $table->integer('dp_24hr')->unsigned()->nullable();
            $table->integer('dp_0hr')->unsigned()->nullable();
            $table->date('tdp_current_date')->nullable();
            //
            //
            $table->date('aps_last_date')->nullable();
            $table->integer('aps_person')->unsigned()->nullable();
            $table->integer('aps_48hr')->unsigned()->nullable();
            $table->integer('aps_24hr')->unsigned()->nullable();
            $table->integer('aps_0hr')->unsigned()->nullable();
            //
            $table->string('electronic_copy_sent',5);
            $table->text('electronic_copy_details')->nullable();
            $table->string('transfer_organised',5);
            $table->text('transfer_organised_details')->nullable();
            $table->date('to_last_date')->nullable();
            $table->integer('to_person')->unsigned()->nullable();
            $table->integer('to_48hr')->unsigned()->nullable();
            $table->integer('to_24hr')->unsigned()->nullable();
            $table->integer('to_0hr')->unsigned()->nullable();
            $table->string('type_of_holidays',50);
            $table->string('sale_person',50);
            $table->integer('deposit_received')->unsigned()->default(0);
            $table->integer('remaining_amount_received')->unsigned()->default(0);
            $table->text('finance_detail')->nullable();
            $table->string('destination',100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //
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
        // DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('bookings');
        // DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
