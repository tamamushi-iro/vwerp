<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('total_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->foreignID('class')->references('id')->on('tags');
            $table->foreignID('category')->references('id')->on('tags');
            $table->foreignID('type')->references('id')->on('tags');
            // $table->string('class');
            // $table->string('category');
            // $table->string('type');
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
        Schema::dropIfExists('items');
    }
}
