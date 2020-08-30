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
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignID('item_id')->constrained()->onDelete('cascade');
            $table->string('serial_number')->unique();
            $table->string('qrcode_path');
            $table->integer('total_quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_lost')->default(false);
            $table->text('notes')->nullable();
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
