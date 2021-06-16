<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'f', 'i' ,'o', 'id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function performers()
    {
        return $this->hasMany('App\Performer','user_id','id');
    }
    public function reconciliations()
    {
        return $this->hasMany('App\Reconciliation','subdivision_id','id');
    }
    public function tickets()
    {
        return $this->hasMany('App\Ticket','subdivision_id','id');
    }
}
