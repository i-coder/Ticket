<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = ['user_id', 'comment', 'ticket_id'];

    /**
     * Связь с моделью "File".
     */
    public function file()
    {
        return $this->hasMany('App\CommentFiles');
    }
}
