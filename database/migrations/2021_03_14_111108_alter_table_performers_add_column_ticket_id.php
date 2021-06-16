<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePerformersAddColumnTicketId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_performers', function ($table) {
            $table->unsignedBigInteger('ticket_id')->nullable('false');
            $table->foreign('ticket_id')->references('id')->on('table_tickets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_performers', function ($table) {
            $table->dropForeign('table_performers_ticket_id_foreign');
            $table->dropColumn('ticket_id');
        });
    }
}
