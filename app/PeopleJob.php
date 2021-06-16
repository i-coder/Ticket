<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeopleJob extends Model
{
    public $table = 'people_job';

    public $fillable = ['id', 'position', 'unit', 'fio', 'domain', 'f', 'i', 'o', 'login'];

}
