<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_support_ticket_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_factured')->nullable()->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_support_ticket_groups');
    }
}