<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            // $table->unsignedInt('order_id');
            $table->string('name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('reporting_date');
            $table->string('location');
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_company');
            $table->string('technician_name');
            $table->string('technician_details');
            $table->string('vehicle_number');
            $table->string('driver_name');
            $table->string('driver_phone');
            $table->string('invoice_number');
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
        Schema::dropIfExists('events');
    }
}
