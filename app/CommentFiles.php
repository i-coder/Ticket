<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentFiles extends Model
{
    public $table = 'comment_files';

    public $fillable = ['file'];


}
