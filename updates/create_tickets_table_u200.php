<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketsTableU200 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('waka_support_tickets', 'wf')) {
                Schema::table('waka_support_tickets', function (Blueprint $table) {
                    $table->string('wf')->nullable();
            });
        }
        
    }

    public function down()
    {
        //
    }
}