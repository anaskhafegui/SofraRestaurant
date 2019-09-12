<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->integer('restaurant_id');
            $table->string('name');
            $table->text('discription');
            $table->decimal('price');
            $table->string('showprice');
            $table->string('processing-time');
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->boolean('disabled')->default(0);

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
