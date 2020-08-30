<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventItemsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_items_history', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignID('event_id');
            $table->foreignID('item_serial_barcode_id');
            $table->integer('assigned_quantity')->default(1);
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
        Schema::dropIfExists('event_items_history');
    }
}
