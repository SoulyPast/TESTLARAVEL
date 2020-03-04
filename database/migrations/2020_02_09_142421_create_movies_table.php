<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('year', 8)->nullable();
            $table->string('director', 64)->nullable();
            $table->string('poster')->nullable();
            $table->boolean('rented')->default(false);
            $table->text('synopsis')->nullable();
            $table->unsignedBigInteger('category_id')->unisgned()->nullable()->default(1);
            $table->timestamps();
        });


        Schema::table('movies', function ($table)
        {
            $table->foreign('category_id')->references('id')->on('categories');
         
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
