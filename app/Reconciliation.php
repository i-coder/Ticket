<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reconciliation extends Model
{
    /**
     * Таблица привязанная к модели.
     *
     * @var string
     */
    protected $table = 'table_reconciliations';

    /**
     * Поля для заполнения.
     *
     * @var string
     */
    protected $fillable = ['subdivision_id'];

    /**
     * Связь с моделью "Подразделение".
     *
     * @return HasOne
     */
    public function subdivision(): HasOne
    {
        return $this->hasOne('App\Subdivision');
    }

    public function tickets()
    {
        return $this->hasMany('App\Ticket','id','ticket_id');
    }
}
