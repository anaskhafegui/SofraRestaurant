<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      
		Schema::create('item_order', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('item_id');
			$table->integer('order_id');
			$table->decimal('price');
			$table->integer('quantity');
			$table->integer('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_order');
    }
}
