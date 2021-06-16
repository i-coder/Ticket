<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_tickets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('date_start');
            $table->string('date_end');
            $table->string('type_task');
            $table->string('priority');
            $table->longText('msg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_tickets');
    }
}
