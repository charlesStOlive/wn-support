<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketsTableU111 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('waka_support_tickets', 'parent_id')) {
                Schema::table('waka_support_tickets', function (Blueprint $table) {
                    $table->integer('parent_id')->unsigned()->nullable();
            });
        }
        
    }

    public function down()
    {
        //
    }
}