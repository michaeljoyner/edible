<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEdibleGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec_galleries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ec_page_id')->unsigned();
            $table->foreign('ec_page_id')->references('id')->on('ec_pages')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->integer('is_single')->default(0);
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
        Schema::drop('ec_galleries');
    }
}
