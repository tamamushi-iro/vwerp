<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemSerialBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_serial_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignID('item_id')->constrained();
            $table->string('serial_number');
            $table->integer('quantity')->default(1);
            $table->string('barcode_number')->default('0000123456789');         // TBR
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
        Schema::dropIfExists('item_serial_barcodes');
    }
}
