<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public $table = 'table_attached_files';

    public $fillable = ['file'];
}
