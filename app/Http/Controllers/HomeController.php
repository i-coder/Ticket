<?php

namespace App\Http\Controllers;

use App\Events\TableAll;
use App\PeopleJob;
use App\Performer;
use App\Reconciliation;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //broadcast(new TableAll('refresh'))->toOthers();
//        broadcast(new TableAll(1))->toOthers();

        return view('welcome', ['users'=>User::all()]);
    }

    public function youTickets()
    {
        return view('you.tickets');
    }

    public function store()
    {
        return view('home');
    }

    public function profile()
    {
        $user = User::where('id', '=', \Auth::id())->first();
        return view('you.profile', ['model' => $user]);
    }

    public function edit(Request $request)
    {
        $user = User::find(\Auth::id());
        $user->f = $request->f;
        $user->i = $request->i;
        $user->o = $request->o;
        $user->save();
        return \Redirect::back()->with('message', 'Profile update succes!');
    }

    public function auth(Request $request)
    {
        $success = [
            'ispl' => false,
            'sogl' => false,
            'zakaz' => false,
        ];

        $isp = Performer::where('ticket_id', '=', $request->id)->where('user_id', '=',\Auth::id())->first();
        if ($isp) {
            $success['ispl'] = true;
        }
        $sog = Reconciliation::where('ticket_id', '=', $request->id)->where('subdivision_id', '=',\Auth::id())->first();
        if ($sog) {
            $success['sogl'] = true;
        }


        $t = Ticket::where('id_user', '=', \Auth::id())->where('id', '=', $request->id)->first();
        if ($t) {
            $success['zakaz'] = true;
        }

        return $success;

    }


}
