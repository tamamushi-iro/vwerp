<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLedCabinetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('led_cabinets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignID('item_id')->constrained()->onDelete('cascade');
            $table->string('serial_number')->unique();
            $table->string('qrcode_path');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_lost')->default(false);
            $table->boolean('in_maintenance')->default(false);
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
        Schema::dropIfExists('led_cabinets');
    }
}
