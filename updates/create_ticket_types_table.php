<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketTypesTable extends Migration
{
    public function up()
    {
        Schema::create('waka_support_ticket_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_facturable')->default(false);
            $table->boolean('is_for_super_user')->nullable();
            $table->text('description')->nullable();
            //reorder
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_support_ticket_types');
    }
}