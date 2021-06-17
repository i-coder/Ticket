<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketProcent extends Model
{
    public $table = 'table_procent_ticket';

    public $fillable = ['id', 'user_id', 'procent', 'ticket_id'];

}
