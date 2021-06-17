<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('index');

    Route::get('/store', 'HomeController@store')->name('store');
    Route::get('tag/{id}', 'TicketController@tag')->name('tag');
    Route::get('all', 'TicketController@all')->name('all');

    Route::get('getListSubdivisionsName', 'TicketController@getListSubdivisionsName')->name('getListSubdivisionsName');
    Route::get('getListSubdivisionsNameCount', 'TicketController@getListSubdivisionsNameCount')->name('getListSubdivisionsNameCount');
    Route::get('getListSubdivisionsNameCountAuth', 'TicketController@getListSubdivisionsNameCountAuth')->name('getListSubdivisionsNameCountAuth');
    Route::get('getListSubdivisionsNameCountAuthWork', 'TicketController@getListSubdivisionsNameCountAuthWork')->name('getListSubdivisionsNameCountAuthWork');
    Route::get('getListPeopleJob', 'TicketController@getListPeopleJob')->name('getListPeopleJob');

    Route::get('logout', 'Auth\LoginController@logout');
    Route::get('migrationUserc1C', 'TicketController@migrationUserc1C')->name('migrationUserc1C');
    Route::get('/view', 'TicketController@view')->name('viewTicket');
    Route::get('/show', 'TicketController@show')->name('showTicket');
    Route::post('/save', 'TicketController@store')->name('storeTicket');
    Route::post('/edit', 'TicketController@edit')->name('editTicket');
    Route::post('/addComment', 'TicketController@addComment')->name('addComment');

    Route::post('/add/status/zakaz', 'TicketController@addStatusZakaz')->name('addStatusZakaz');
    Route::post('/add/status/ispl', 'TicketController@addStatusIspl')->name('addStatusIspl');
    Route::post('/add/status/sogl', 'TicketController@addStatusSogl')->name('addStatusSogl');
    Route::post('/real/status/ticket', 'TicketController@realStatusTicket')->name('realStatusTicket');

    Route::post('/deleteExistingFile', 'TicketController@deleteExistingFile')->name('deleteExistingFile');

    Route::prefix('/approval')->group(function () {
        Route::get('/', 'ApprovalController@listTicketsForApproval')->name('listTicketsForApproval');
    });


    Route::prefix('/you')->group(function () {
        Route::get('/tickets', 'HomeController@youTickets')->name('listTicketsForYou');
        Route::get('/tickets/not/agree', 'TicketController@youTicketsNotAgree')->name('youTicketsNotAgree');
        Route::get('/tickets/performers', 'TicketController@youTicketsPerformers')->name('youTicketsPerformers');
        Route::get('/all/tickets', 'TicketController@youTickets');
        Route::get('/profile', 'HomeController@profile');
        Route::post('/profile/edit', 'HomeController@edit')->name('you/profile/edit');
        Route::post('/auth', 'HomeController@auth')->name('you/auth');
    });
    Route::prefix('/all')->group(function () {
        Route::get('/people/work', 'TicketController@allPeopleWork')->name('listTicketsForYou');
        Route::get('/people/show', 'TicketController@showPeopleWork')->name('listTicketsForYou');
        Route::get('/people/ticket', 'TicketController@dataPeopleWork')->name('dataPeopleWork');
        Route::get('/people/reconciliations', 'TicketController@dataPeopleReconciliations')->name('dataPeopleReconciliations');
    });
});

//Route::post('auth', 'TicketController@auth')->name('auth');
Route::get('errorAuth1c', 'TicketController@errorAuth1C')->name('error_auth_1c');
