<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEdibleTextblocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec_textblocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ec_page_id')->unsigned();
            $table->foreign('ec_page_id')->references('id')->on('ec_pages')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->text('content')->nullable();
            $table->boolean('allows_html');
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
        Schema::drop('ec_textblocks');
    }
}
