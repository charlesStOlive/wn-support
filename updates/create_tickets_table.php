<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_support_tickets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('silent_mode')->nullable();
            $table->string('name');
            $table->integer('ticket_type_id')->unsigned();
            $table->string('state')->nullable();
            $table->integer('user_id')->unsigned();
            $table->string('url')->nullable();
            $table->string('temps')->nullable();
            //reorder
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_support_tickets');
    }
}