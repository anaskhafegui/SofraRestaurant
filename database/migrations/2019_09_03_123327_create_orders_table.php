<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('restaurant_id');
            $table->integer('payment_method_id');
			$table->text('note')->nullable();
			$table->text('address');
			$table->decimal('cost')->default(0.00);
			$table->decimal('delivery_cost')->default(0.00);
			$table->decimal('total')->default(0.00);
			$table->datetime('need_delivery_at');
			$table->integer('delivery_time_id');
			$table->datetime('delivered_at')->nullable();
			$table->enum('state', array('pending', 'accepted', 'rejected'));
			$table->boolean('delivery_confirmed_by_restaurant')->default(0);
			$table->boolean('delivery_confirmed_by_client')->default(0);
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
        Schema::dropIfExists('orders');
    }
}
