<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubdivisionName extends Model
{
    protected $table = 'subdivisions_name';

    protected $fillable = ['id', 'name'];

    public function customers()
    {
        return $this->hasMany('App\Ticket', 'customer', 'id');
    }

    public function customersAuth()
    {
        return $this->hasMany('App\Ticket', 'customer', 'id')->where('id_user', '=', \Auth::id());
    }


}
