<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    public $table = 'ticket_status';

    //роли
    const ZAKA = 1;
    const ISPL = 2;
    const SOGL = 3;

    //исполнители статусы
    const SDELA = 1;
    const VRABO = 2;
    const NEGOT = 3;
    const TESTI = 4;
    const PAUZA = 5;

    //согласование статусы
    const SOGLASOVANO = 2;
    const NESOGLASOVANO = 1;

    public $fillable = ['id', 'roll', 'user_id', 'status', 'created_at', 'updated_at', 'ticket_id'];

}
