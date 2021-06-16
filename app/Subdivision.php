<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subdivision extends Model
{
    protected $table = 'table_subdivisions';

    protected $fillable = ['title'];
}
