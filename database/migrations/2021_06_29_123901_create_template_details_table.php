<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('booking_method_id');
            $table->unsignedBigInteger('booked_by_id');
            $table->unsignedBigInteger('supervisor_id');
            $table->string('booking_refrence');
            $table->enum('booking_type', ['refundable', 'non_refundable']);
            $table->string('comments');
            $table->string('supplier_currency');
            $table->double('qoute_base_currency');
            $table->double('estimated_cost');
            $table->double('currency_conversion');
            $table->enum('added_in_sage', [0, 1])->default(0);
            $table->date('date_of_service');
            $table->date('service_details');
            $table->date('booking_date');
            $table->date('booking_due_date');
            $table->timestamps();
            
            $table->foreign('template_id')->references('id')->on('templates')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('booked_by_id')->references('id')->on('users');
            $table->foreign('supervisor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_details');
    }
}
