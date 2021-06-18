<?php

namespace App\Http\Controllers;

use App\Reconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Контроллер для раздела на согласование.
 *
 * Class ApprovalController
 * @package App\Http\Controllers
 */
class ApprovalController extends Controller
{
    /**
     * Показать все тикеты для согласовния
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listTicketsForApproval()
    {
        return view('approval.list');

    }
}
