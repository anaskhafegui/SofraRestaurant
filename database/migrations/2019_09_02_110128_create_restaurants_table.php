<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->integer('city_id');
            $table->string('password');
            $table->integer('delivery_category_id');
            $table->decimal('minimum_charger') ;
            $table->decimal('delivery_cost');
           // $table->text('delivery_days');
			$table->string('whatsapp')->nullable();
			$table->string('photo')->nullable();
			$table->enum('availability', array('open', 'closed'));
			$table->string('api_token',60);
            $table->string('code',6)->nullable();
            $table->boolean('activated')->default(1);
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
        Schema::dropIfExists('restaurants');
    }
}
