<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketsTableU110 extends Migration
{
    public function up()
    {
        Schema::table('waka_support_tickets', function (Blueprint $table) {
            $table->boolean('urgent')->nullable();
            $table->string('code')->nullable();
            $table->integer('support_user_id')->unsigned()->nullable();
            $table->integer('support_client_id')->unsigned()->nullable();
            $table->integer('next_id')->unsigned()->nullable();
            $table->integer('ticket_group_id')->unsigned()->nullable();
            $table->date('awake_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('waka_support_tickets', function (Blueprint $table) {
            $table->dropColumn('urgent');
            $table->dropColumn('code');
            $table->dropColumn('support_user_id');
            $table->dropColumn('support_client_id');
            $table->dropColumn('next_id');
            $table->dropColumn('ticket_group_id');
            $table->dropColumn('awake_at');
        });
    }
}