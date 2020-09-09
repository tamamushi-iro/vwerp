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
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('invoice_number')->nullable();
            $table->string('name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('reporting_date');
            $table->string('location');
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone');
            $table->string('client_company');
            $table->string('technician_name')->nullable();
            $table->string('technician_phone')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('priority')->default('04a9f5');
            $table->boolean('has_ended')->default(false);
            $table->boolean('is_final')->default(false);
            $table->boolean('mail_sent')->default(false);
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
