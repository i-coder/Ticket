<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Performer extends Model
{
    public $table = 'table_performers';

    public $fillable = ['first_name', 'last_name', 'employee_id', 'user_id'];
    public function tickets()
    {
        return $this->hasMany('App\Ticket','id','ticket_id');
    }
    public function getStatus()
    {
        return $this->hasMany('App\TicketStatus','ticket_id', 'ticket_id')->where('user_id','=',Auth::id());
    }
}
