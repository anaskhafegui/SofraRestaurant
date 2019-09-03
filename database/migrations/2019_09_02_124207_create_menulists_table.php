<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenulistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menulists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('restaurant_id');
            $table->string('name');
            $table->text('discription');
            $table->decimal('price');
            $table->string('showprice');
            $table->string('processing-time');
            $table->string('photo')->nullable();
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
        Schema::dropIfExists('menulists');
    }
}
