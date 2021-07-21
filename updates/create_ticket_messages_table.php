<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('waka_support_ticket_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('ticket_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_support_ticket_messages');
    }
}