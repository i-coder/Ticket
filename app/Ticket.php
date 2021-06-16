<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $table = 'table_tickets';

    public $fillable = ['date_start', 'customer', 'date_end', 'msg', 'type_task', 'priority', 'title', 'id_user', 'status', 'delete'];

    const URLAGENCY = 'http://146.158.79.84:8835/july_2020/ws/WSAgency.1cws?wsdl';
    const USERAGENCY = 'userwww';
    const USERPASSWORD = '123';


    const STATUS_UNAPPROVED = 0;

    const STATUS_APPROVED = 1;

    const DELETED = 1;

    const NOT_DELETED = 0;

    /**
     * Связь с моделью "Subdivision".
     */
    public function reconciliation()
    {
        return $this->hasMany('App\Reconciliation');
    }

    /**
     * Связь с моделью "Performer".
     */
    public function performer()
    {
        return $this->hasMany('App\Performer');
    }
    public function statusTicketIspl()
    {
        return $this->hasMany('App\TicketStatus', 'ticket_id', 'id')->where('roll','=',TicketStatus::ISPL);
    }
    public function statusTicketIsplGroup()
    {
        return $this->hasMany('App\TicketStatus', 'ticket_id', 'id')->where('roll','=',TicketStatus::ISPL)->groupBy('user_id');
    }
    public function statusTicketSogl()
    {
        return $this->hasMany('App\TicketStatus', 'ticket_id', 'id')->where('roll','=',TicketStatus::SOGL);
    }
    public function statusTicketAll()
    {
        return $this->hasMany('App\TicketStatus', 'ticket_id', 'id');
    }
    public function statusTicketAllAuth()
    {
        return $this->hasMany('App\TicketStatus', 'ticket_id', 'id')->where('user_id','=',\Auth::id());
    }
    //получаем не согласованные тикеты
    public function statusTicketSoglAuth()
    {
        return $this->hasMany('App\TicketStatus', 'ticket_id', 'id')
            ->where('roll','=',TicketStatus::SOGL)
            ->where('user_id','=',\Auth::id())
            ->where('status','=',TicketStatus::NESOGLASOVANO);
    }

    /**
     * Связь с моделью "File".
     */
    public function file()
    {
        return $this->hasMany('App\File');
    }
    /**
     * Связь с моделью "File".
     */
    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('id','ASC');
    }

    /**
     * Авторизация в 1С (AD)
     */
    public function getUser1C($data)
    {

        try {
            $parameters = [
                'login' => self::USERAGENCY,
                'password' => self::USERPASSWORD,
                'trace' => true,
                'cache_wsdl' => WSDL_CACHE_NONE
            ];
            $soapClient = new \SoapClient(self::URLAGENCY, $parameters);


            $response = $soapClient->GetAccount((object)[
                'ID' => $data['login'],
                'Pass' => $data['password']
            ]);

            return $response->return;

        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 405);
        }
    }
    /**
     * Получение списка пользователей из системы 1С (AD)
     */
    public function getListAccounts1C()
    {

        try {
            $parameters = [
                'login' => self::USERAGENCY,
                'password' => self::USERPASSWORD,
                'trace' => true,
                'cache_wsdl' => WSDL_CACHE_NONE
            ];
            $soapClient = new \SoapClient(self::URLAGENCY, $parameters);


            $response = $soapClient->GetListAccounts();

            return $response->return;

        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 405);
        }
    }


}
