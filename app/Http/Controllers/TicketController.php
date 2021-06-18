<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentFiles;
use App\File;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\PeopleJob;
use App\Performer;
use App\Reconciliation;
use App\Subdivision;
use App\SubdivisionName;
use App\Ticket;
use App\TicketProcent;
use App\TicketStatus;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TicketController extends Controller
{
    use AuthenticatesUsers;

    public $ticket1C;

    const STATUSES = [
        1 => 'agreed',
        0 => 'not agreed',
    ];

    protected $priorities = [
        1 => 'Срочно',
        2 => 'Средний',
        3 => 'Текучка',
    ];

    protected $tasks = [
        0 => 'Техническое задание',
        1 => 'Согласование',
        2 => 'Задача',
        3 => 'Служебка',
    ];

    protected $fileUploadPath = 'uploads/ticket_attached_files/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->ticket1C = new Ticket();
    }

    /**
     * Show the application dashboard.
     */
    public function store(Request $request)
    {

        $date_start = '';
        $date_end = '';
        $isReconcilationSaved = true;
        $isTicketSaved = true;
        $isPerformerSaved = true;

        $dates = explode(',', $request->get('date', ''));
        $type_task = $request->get('task', '');
        $priority = $request->get('priority', '');
        $msg = $request->get('msg', '');
        $title = $request->get('title', '');
        $performers = json_decode($request->get('performers', ''));
        $reconciliations = json_decode($request->get('reconciliations', ''));
        $uploadedFiles = $request->allFiles();

        $files = [];


        if (count($dates) == 2) {
            $date_start = $dates[0];
            $date_end = $dates[1];
        }

        $ticketData = [
            'title' => $title,
            'date_start' => explode(',', $request->date_start)[0],
            'date_end' => explode(',', $request->date_end)[0],
            'type_task' => $type_task,
            'priority' => $priority,
            'msg' => $msg,
            'id_user' => Auth::id() ?? 0,
            'status' => Ticket::STATUS_UNAPPROVED,
            'delete' => Ticket::NOT_DELETED,
            'customer' => $request->customer
        ];

        $newTicket = Ticket::create($ticketData);

//        try {
//            $request->validate([
//                'file.*' => 'mimes:jpeg,png,txt,pdf,xls,jpg,bmp,gif,doc,docx,pdf',
//            ]);
//        } catch (\Exception $e) {
//            return redirect()->back()->with('error', 'Неправильный формат загруженного файла');
//        }

        if (isset($uploadedFiles['files'])) {
            foreach ($uploadedFiles['files'] as $uploadedFile) {
                $fileName = date('d_m_y') . '_' . rand(11111, 99999) . '.' . $uploadedFile->extension();

                $path = (public_path($this->fileUploadPath . $newTicket->id));

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $uploadedFile->move($path, $fileName);

                array_push($files, $fileName);

                $newFile = File::create(['file' => serialize($files)]);

                $isTicketSaved = $newTicket->file()->save($newFile);
            }
        }


        foreach ($performers as $performer) {
            $user = User::where('f', '=', $performer->f)->where('i', '=', $performer->i)->first();
            if ($user) {
                $user_id = $user['id'];
            } else {
                $user_id = 0;
            }
            $performersData = [
                'first_name' => $performer->f,
                'last_name' => $performer->i,
                'employee_id' => $performer->id,
                'user_id' => $user_id,
            ];

            $performer = new Performer($performersData);

            $isPerformerSaved = $newTicket->performer()->save($performer);
        }

        foreach ($reconciliations as $reconciliation) {
            $reconciliationsData = [
                'subdivision_id' => $reconciliation->id
            ];

            $reconciliation = new Reconciliation($reconciliationsData);

            $isReconcilationSaved = $newTicket->reconciliation()->save($reconciliation);
        }

        return $isReconcilationSaved && $isPerformerSaved && $isTicketSaved;
    }

    /**
     *  Редактирование
     */
    public function edit(Request $request)
    {
        $id = (int)$request->get('id');
        $existingTicket = Ticket::find($id);
        $date_start = '';
        $date_end = '';
        $isReconcilationSaved = true;
        $isTicketSaved = true;
        $isPerformerSaved = true;

        $dates = explode(',', $request->get('date', ''));
        $type_task = $request->get('task', '');
        $priority = $request->get('priority', '');
        $msg = $request->get('msg', '');
        $title = $request->get('title', '');
        $performers = json_decode($request->get('performers', ''));
        $reconciliations = json_decode($request->get('reconciliations', ''));
        $uploadedFiles = $request->allFiles();
        $files = [];

        if (count($dates) == 2) {
            $date_start = $dates[0];
            $date_end = $dates[1];
        }

        $ticketData = [
            'title' => $title,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'type_task' => $type_task,
            'priority' => $priority,
            'msg' => $msg,
            'id_user' => Auth::id() ?? 0,
            'status' => Ticket::STATUS_UNAPPROVED,
            'delete' => Ticket::NOT_DELETED,
        ];

        $existingTicket->update($ticketData);

        try {
            $request->validate([
                'file.*' => 'mimes:jpeg,png,txt,pdf,xls,jpg,bmp,gif,doc,docx,pdf',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Неправильный формат загруженного файла');
        }

        if (isset($uploadedFiles['files'])) {
            foreach ($uploadedFiles['files'] as $uploadedFile) {
                $fileName = date('d_m_y') . '_' . rand(11111, 99999) . '.' . $uploadedFile->extension();

                $path = (public_path($this->fileUploadPath . $existingTicket->id));

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $uploadedFile->move($path, $fileName);

                array_push($files, $fileName);

                $newFile = File::create(['file' => serialize($files)]);

                $isTicketSaved = $existingTicket->file()->save($newFile);
            }
        }

        $existingTicket->reconciliation()->delete();

        foreach ($reconciliations as $reconciliation) {
            $reconciliationsData = [
                'subdivision_id' => $reconciliation
            ];

            $reconciliation = new Reconciliation($reconciliationsData);

            $isReconcilationSaved = $existingTicket->reconciliation()->save($reconciliation);
        }

        $existingTicket->performer()->delete();

        foreach ($performers as $performer) {
            $performersData = [
                'first_name' => $performer->first_name,
                'last_name' => $performer->last_name,
                'employee_id' => $performer->id,
            ];

            $performer = new Performer($performersData);

            $isPerformerSaved = $existingTicket->performer()->save($performer);
        }

        return $isReconcilationSaved && $isPerformerSaved && $isTicketSaved;
    }

    /**
     * Удаление существующих файлов
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function deleteExistingFile(Request $request): array
    {
        $fileId = (int)$request->get('fileId', '');
        $ticketId = (int)$request->get('ticketId', '');
        $ticket = Ticket::find($ticketId);
        $fileLinks = [];

        if ($ticket instanceof Ticket) {
            $file = $ticket->file->find($fileId);

            if ($file instanceof File) {
                $file->delete();
            }
        }

        $files = $ticket->file ? $ticket->file->all() : [];

        if (!empty($files)) {
            foreach ($files as $file) {
                $fileName = current(unserialize($file->file));

                $fileLinks[] = (object)[
                    'id' => $file->id,
                    'path' => '/uploads/ticket_attached_files/' . $ticket->id . '/' . $fileName,
                    'name' => $fileName
                ];
            }
        }

        return $fileLinks;
    }

    public function tag($id)
    {

    }

    /**
     *  Добавляет комментарий.
     *
     * @param Request $request
     */
    public function addComment(Request $request)
    {

        $newComment = [
            'ticket_id' => $request->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'files' => ''
        ];

        $newIdComment = Comment::create($newComment);

        try {
            $request->validate([
                'file.*' => 'mimes:jpeg,png,txt,pdf,xls,jpg,bmp,gif,doc,docx,pdf',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Неправильный формат загруженного файла');
        }

        $uploadedFiles = $request->allFiles();
        $files = [];

        if (isset($uploadedFiles['files'])) {
            foreach ($uploadedFiles['files'] as $uploadedFile) {
                $fileName = date('d_m_y') . '_' . rand(11111, 99999) . '.' . $uploadedFile->extension();
                $rd = rand(10000, 900000) . $newIdComment->id;
                $path = (public_path($this->fileUploadPath . '/' . $request->id));

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $uploadedFile->move($path, $fileName);
                array_push($files, $fileName);
                $newFile = CommentFiles::create(['file' => $fileName]);
                $newIdComment->file()->save($newFile);
            }
        }

        $realName = User::find(Auth::id());
        return json_encode(['id' => $newIdComment->id, 'comment' => $request->comment, 'date' => $newIdComment->created_at, 'files' => json_encode($files), 'users' => $realName->f.' '.$realName->i.' '.$realName->o ]);

    }

    public
    function view(Request $request)
    {
        $id = (int)$request->get('id');
        $ticket = Ticket::find($id);
        $performers = $ticket->performer ? $ticket->performer->all() : [];
        $reconciliations = $ticket->reconciliation ? $ticket->reconciliation->all() : [];
        $files = $ticket->file ? $ticket->file->all() : [];
        $performers_data = [];
        $reconciliations_data = [];
        $fileLinks = [];


        if (!empty($performers)) {
            foreach ($performers as $performer) {
                $performers_data[] = (object)[
                    'id' => $performer->employee_id,
                    'first_name' => $performer->first_name,
                    'last_name' => $performer->last_name,
                ];
            }
        }

        if (!empty($reconciliations)) {
            foreach ($reconciliations as $reconciliation) {
                $reconciliations_data[] = $reconciliation->subdivision_id;
            }
        }

        if (!empty($files)) {
            foreach ($files as $file) {
                $fileName = current(unserialize($file->file));

                $fileLinks[] = [
                    'id' => $file->id,
                    'path' => '/uploads/ticket_attached_files/' . $ticket->id . '/' . $fileName,
                    'name' => $fileName
                ];
            }
        }

        return view('ticket.view', ['ticket' => $ticket, 'performers' => $performers_data, 'reconciliations' => $reconciliations_data, 'files' => $fileLinks]);
    }

    public
    function show(Request $request)
    {

        $id = (int)$request->get('id');
        $ticket = Ticket::find($id);

        $statusIspl = $ticket->statusTicketIspl;

        if ($statusIspl->toArray()) {
            $ticket['ispl'] = (int)$statusIspl->toArray()[0]['status'];
        } else {
            $ticket['ispl'] = null;
        }


        $ticketStZakaz = TicketStatus::where('ticket_id', '=', $id)->where('roll', '=', TicketStatus::ZAKA)->orderBy('id', 'DESC')->get();
        $ticketStIspl = TicketStatus::where('ticket_id', '=', $id)->where('roll', '=', TicketStatus::ISPL)->orderBy('id', 'DESC')->get();
        $ticketStSogl = TicketStatus::where('ticket_id', '=', $id)->where('roll', '=', TicketStatus::SOGL)->orderBy('id', 'DESC')->get();
        $ticketProcentSpl = TicketProcent::where('ticket_id', '=', $id)->orderBy('id', 'DESC')->get();

        $arrZakazStatus = [];
        $arrIsplStatus = [];
        $arrSoglStatus = [];
        $arrIsplProcent = [];

        $b = 0;
        foreach ($ticketProcentSpl as $item) {
            $u = User::find($item->user_id);
            if (empty($u->f) or empty($u->i)) {
                $arrIsplProcent[$b]['user'] = 'Неизвестно';
            } else {
                $arrIsplProcent[$b]['user'] = $u->f . ' ' . $u->i . ' ' . $u->o;
            }
            $arrIsplProcent[$b]['procent'] = $item->procent;
            $arrIsplProcent[$b]['created_at'] = \Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s');//TODO время карбон!
            $b++;
        }
        $w = 0;
        foreach ($ticketStZakaz as $item) {
            $u = User::find($item->user_id);
            if (empty($u->f) or empty($u->i)) {
                $arrZakazStatus[$w]['user'] = 'Неизвестно';
            } else {
                $arrZakazStatus[$w]['user'] = $u->f . ' ' . $u->i . ' ' . $u->o;
            }
            $arrZakazStatus[$w]['status'] = $item->status;
            $arrZakazStatus[$w]['roll'] = $item->roll;
            $arrZakazStatus[$w]['created_at'] = \Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s');//TODO время карбон!
            $w++;
        }
        $t = 0;
        foreach ($ticketStIspl as $item) {
            $u = User::find($item->user_id);
            if (empty($u->f) or empty($u->i)) {
                $arrIsplStatus[$t]['user'] = 'Неизвестно';
            } else {
                $arrIsplStatus[$t]['user'] = $u->f . ' ' . $u->i . ' ' . $u->o;
            }

            $arrIsplStatus[$t]['status'] = $item->status;
            $arrIsplStatus[$t]['roll'] = $item->roll;
            $arrIsplStatus[$t]['created_at'] = \Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s');//TODO время карбон!
            $t++;
        }

        $i = 0;
        foreach ($ticketStSogl as $item) {
            $u = User::find($item->user_id);
            if (empty($u->f) or empty($u->i)) {
                $arrSoglStatus[$i]['user'] = 'Неизвестно';
            } else {
                $arrSoglStatus[$i]['user'] = $u->f . ' ' . $u->i . ' ' . $u->o;
            }
            $arrSoglStatus[$i]['status'] = $item->status;
            $arrSoglStatus[$i]['roll'] = $item->roll;
            $arrSoglStatus[$i]['created_at'] = \Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s');//TODO время карбон!
            $i++;
        }

        $statusSogl = $ticket->statusTicketSogl;
        if ($statusSogl->toArray()) {
            $ticket['sogl'] = (int)$statusSogl->toArray()[0]['status'];
        } else {
            $ticket['sogl'] = null;
        }


        $performers = $ticket->performer ? $ticket->performer->all() : [];//исполнители
        $reconciliations = $ticket->reconciliation ? $ticket->reconciliation->all() : [];//согласующие

        $files = $ticket->file ? $ticket->file->all() : [];
        $performers_data = [];
        $comments_data = [];
        $reconciliations_data = [];
        $fileLinks = [];
        $commetns = $ticket->comments ? $ticket->comments->all() : [];


        if (!empty($commetns)) {

            foreach ($commetns as $comment) {
                $toArray = [];
                if (!empty($comment->file)) {
                    foreach ($comment->file as $item) {
                        $toArray[] = $item->file;
                    }
                }
                $fio = User::where('id','=',$comment->user_id)->first();
                $comments_data[] = (object)[
                    'id' => $comment->id,
                    'ticket_id' => $comment->ticket_id,
                    'user_id' => $comment->user_id,
                    'users' => $fio->f . ' ' . $fio->i . ' ' . $fio->o,
                    'comment' => $comment->comment,
                    'date' => $comment->created_at,
                    'files' => $toArray
                ];

            }

        }

        if (!empty($performers)) {
            foreach ($performers as $performer) {
                $p = User::where('id', '=', $performer->user_id)->first();
                $performers_data[] = (object)[
                    'id' => $p['id'],
                    'fio' => $p['f'] . ' ' . $p['i'] . ' ' . $p['0'],
                ];
            }
        }

        if (!empty($reconciliations)) {
            foreach ($reconciliations as $performer) {

                $p = User::where('id', '=', $performer->subdivision_id)->first();
                $reconciliations_data[] = (object)[
                    'id' => $p['id'],
                    'fio' => $p['f'] . ' ' . $p['i'] . ' ' . $p['0'],
                ];
            }
        }

        if (!empty($files)) {
            foreach ($files as $file) {
                $fileName = current(unserialize($file->file));

                $fileLinks[] = [
                    'id' => $file->id,
                    'path' => '/uploads/ticket_attached_files/' . $ticket->id . '/' . $fileName,
                    'name' => $fileName
                ];
            }
        }

        $tekStatusSogl = DB::table('ticket_status')
            ->where('ticket_id', '=', $request->id)
            ->where('roll', TicketStatus::SOGL)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();
        $tekStatusIspl = DB::table('ticket_status')
            ->where('ticket_id', '=', $request->id)
            ->where('roll', TicketStatus::ISPL)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();
        $tekStatusZakaz = DB::table('ticket_status')
            ->where('ticket_id', '=', $request->id)
            ->where('roll', TicketStatus::ZAKA)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();
        $tekProcentIspl = DB::table('table_procent_ticket')
            ->where('ticket_id', '=', $request->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($tekStatusZakaz) {
            $ticket['tekStatusZakaz'] = $tekStatusZakaz->status;
        } else {
            $ticket['tekStatusZakaz'] = null;
        }
        if ($tekStatusIspl) {
            $ticket['tekStatusIspl'] = $tekStatusIspl->status;
        } else {
            $ticket['tekStatusIspl'] = null;
        }
        if ($tekStatusSogl) {
            $ticket['tekStatusSogl'] = $tekStatusSogl->status;
        } else {
            $ticket['tekStatusSogl'] = null;
        }
        //процент исполнения
        if ($tekProcentIspl) {
            $ticket['tekProcentIspl'] = $tekProcentIspl->procent;
        } else {
            $ticket['tekProcentIspl'] = null;
        }


        $customer = SubdivisionName::find($ticket->customer);
//dd(['zakaz' => $arrZakazStatus, 'procentispl'=>$arrIsplProcent, 'ispl' => $arrIsplStatus, 'sogl' => $arrSoglStatus, 'customer' => $customer, 'comments' => $comments_data, 'user' => User::find($ticket->id_user), 'ticket' => $ticket, 'performers' => $performers_data, 'reconciliations' => $reconciliations_data, 'files' => $fileLinks]);
        return view('ticket.show', ['zakaz' => $arrZakazStatus, 'procentispl'=>$arrIsplProcent, 'ispl' => $arrIsplStatus, 'sogl' => $arrSoglStatus, 'customer' => $customer, 'comments' => $comments_data, 'user' => User::find($ticket->id_user), 'ticket' => $ticket, 'performers' => $performers_data, 'reconciliations' => $reconciliations_data, 'files' => $fileLinks]);
    }

    public function realStatusTicket(Request $request)
    {
        $ticket = Ticket::find($request->id);
        $cou = DB::table('ticket_status')
            ->where('ticket_id', '=', $request->id)
            ->where('status', TicketStatus::SOGLASOVANO)
            ->where('roll', TicketStatus::SOGL)
            ->orderBy('created_at', 'desc')
            ->get();
        $r = [];
        foreach ($cou as $item) {
            $r[$item->user_id] = $item->user_id;
        }

        return count($r) . '/' . count($ticket->reconciliation);
    }

    /*
     * Процент выполненой работы
     */
    public function addProcentIspl(Request $request)
    {

        $isplProcent = (int)$request->procent;
        $id = (int)$request->id;

        if ((int)$isplProcent != "null") {
            $max = TicketProcent::where('ticket_id','=',$id)->max('procent');

            if ($max < $isplProcent) {
                $newStatusSogl = [
                    'user_id' => Auth::id(),
                    'procent' => $isplProcent,
                    'ticket_id' => $id,
                ];
                TicketProcent::create($newStatusSogl);
            }

        }
    }

    public
    function all(Request $request)
    {

        $findProperty = explode("&", $request->data);
        $findCategory = explode("=", $findProperty[3]);

        $ticketsInfo = [];
        $performersInfo = [];

        if ($findCategory[1] > 1) {
            $tickets = Ticket::where('customer', '=', $findCategory[1])->get();
        } else {
            $tickets = Ticket::all();
        }


        foreach ($tickets as $ticket) {

            $priority = $this->priorities[$ticket->priority] ?? '';
            $type_task = $this->tasks[$ticket->type_task] ?? '';
            $performers = $ticket->performer->all();
            setlocale(LC_TIME, 'ru_RU.UTF-8');
            $date_start = strftime('%d.%m.%y', strtotime($ticket->date_start));
            $date_end = strftime('%d.%m.%y', strtotime($ticket->date_end));
            $actions = '<a href="/show?id=' . $ticket->id . '"> Открыть </a>';
            // $status = self::STATUSES[$ticket->status];

            $tekStatusSogl = DB::table('ticket_status')
                ->where('ticket_id', '=', $ticket->id)
                ->where('roll', TicketStatus::SOGL)
                ->orderBy('created_at', 'desc')
                ->first();

            $tekStatusIspl = DB::table('ticket_status')
                ->where('ticket_id', '=', $ticket->id)
                ->where('roll', TicketStatus::ISPL)
                ->orderBy('created_at', 'desc')
                ->first();

            foreach ($performers as $performer) {
                array_push($performersInfo, [' ' . $performer->first_name . ' ' . $performer->last_name]);
            }

            array_push(
                $ticketsInfo,
                [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'priority' => $priority,
                    'type_task' => $type_task,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'performers' => $performersInfo,
                    'actions' => $actions,
                    'work_status' => ($tekStatusIspl) ? (int)$tekStatusIspl->status : null,
                    'sogl_status' => ($tekStatusSogl) ? (int)$tekStatusSogl->status : null,
                ]
            );

            //обнуляем
            $performersInfo = [];
        }


        if (count($ticketsInfo) == 0) {
            return [];
        }
//
//        $sort = array();
//        foreach ($ticketsInfo as $k => $v) {
//            $sort['status'][$k] = $v['status'];
//            $sort['date_start'][$k] = $v['date_start'];
//        }
//        array_multisort($sort['status'], SORT_ASC, $sort['date_start'], SORT_ASC, $ticketsInfo);

        return json_encode($ticketsInfo);
    }

    function workPeople(Request $request)
    {

        $findProperty = explode("&", $request->data);
        $findCategory = explode("=", $findProperty[3]);

        $ticketsInfo = [];
        $performersInfo = [];

        if ($findCategory[1] > 1) {
            $tickets = Ticket::where('customer', '=', $findCategory[1])->get();
        } else {
            $tickets = Ticket::all();
        }


        foreach ($tickets as $ticket) {

            $priority = $this->priorities[$ticket->priority] ?? '';
            $type_task = $this->tasks[$ticket->type_task] ?? '';
            $performers = $ticket->performer->all();
            setlocale(LC_TIME, 'ru_RU.UTF-8');
            $date_start = strftime('%d.%m.%y', strtotime($ticket->date_start));
            $date_end = strftime('%d.%m.%y', strtotime($ticket->date_end));
            $actions = '<a href="/show?id=' . $ticket->id . '"> Открыть </a>';
            // $status = self::STATUSES[$ticket->status];

            $tekStatusSogl = DB::table('ticket_status')
                ->where('ticket_id', '=', $ticket->id)
                ->where('roll', TicketStatus::SOGL)
                ->orderBy('created_at', 'desc')
                ->first();

            $tekStatusIspl = DB::table('ticket_status')
                ->where('ticket_id', '=', $ticket->id)
                ->where('roll', TicketStatus::ISPL)
                ->orderBy('created_at', 'desc')
                ->first();

            foreach ($performers as $performer) {
                array_push($performersInfo, [' ' . $performer->first_name . ' ' . $performer->last_name]);
            }

            array_push(
                $ticketsInfo,
                [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'priority' => $priority,
                    'type_task' => $type_task,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'performers' => $performersInfo,
                    'actions' => $actions,
                    'work_status' => ($tekStatusIspl) ? (int)$tekStatusIspl->status : null,
                    'sogl_status' => ($tekStatusSogl) ? (int)$tekStatusSogl->status : null,
                ]
            );

            //обнуляем
            $performersInfo = [];
        }


        if (count($ticketsInfo) == 0) {
            return [];
        }

//        $sort = array();
//        foreach ($ticketsInfo as $k => $v) {
//            $sort['status'][$k] = $v['status'];
//            $sort['date_start'][$k] = $v['date_start'];
//        }
//        array_multisort($sort['status'], SORT_ASC, $sort['date_start'], SORT_ASC, $ticketsInfo);

        return json_encode($ticketsInfo);
    }


    /**
     * Получение списка подразделений
     */
    public
    function getListSubdivisionsName()
    {
        $one = SubdivisionName::where('id', '>', 1)->get();
        $menu = [];
        $i = 0;
        $p = 0;
        foreach ($one as $item) {
            $co = count($item->customers);
            $item['count_ticket'] = $co;
            $i = $i + $co;
            array_push($menu, $item);
            foreach ($item->customers as $customer) {
                if (count($customer->statusTicketAll) == 0) {
                    $p++;
                };
            }
        }
        $r = [
            'menus' => $menu,
            'noStatusTicket' => $p,
        ];
        return $r;
    }

    /**
     * Получение списка подразделений на согалсование
     */
    public
    function getListSubdivisionsNameCountAuth()
    {
        $one = SubdivisionName::where('id', '>', 1)->get();
        $menu = [];
        $i = 0;
        $p = 0;
        foreach ($one as $item) {
            $co = count($item->customersAuth);
            $item['count_ticket'] = $co;
            $i = $i + $co;
            array_push($menu, $item);
            foreach ($item->customersAuth as $customer) {
                if (count($customer->statusTicketAllAuth) == 0) {
                    $p++;
                };
            }
        }
        $r = [
            'menus' => $menu,
            'noStatusTicket' => $p,
        ];
        return $r;

    }

    public
    function getListSubdivisionsNameCountAuthWork()
    {
        $one = SubdivisionName::where('id', '>', 1)->get();

        $menu = [];
        foreach ($one as $item) {
            if ($item->customers) {
                $co = '';

                foreach ($item->customersAuth as $items) {
                    $co = count(Performer::where('ticket_id', '=', $items->id)->get());


                }
                $item['count_ticket'] = $co;
                array_push($menu, $item);
            }
        }

        return $menu;

    }

    /**
     * Получение списка работников
     */
    public
    function getListPeopleJob()
    {
        return User::all();
    }

    public
    function youTickets(Request $request)
    {

        $findProperty = explode("&", $request->data);
        $findCategory = explode("=", $findProperty[3]);

        $ticketsInfo = [];
        $performersInfo = [];

        if ($findCategory[1] > 1) {
            $tickets = Ticket::where('customer', '=', $findCategory[1])->where('id_user', '=', Auth::id())->get();
        } else {
            $tickets = Ticket::where('id_user', '=', Auth::id())->get();
        }

        //$tickets = Ticket::where('id_user', '=', Auth::id())->get();

        foreach ($tickets as $ticket) {
            $priority = $this->priorities[$ticket->priority] ?? '';
            $type_task = $this->tasks[$ticket->type_task] ?? '';
            $performers = $ticket->performer->all();
            setlocale(LC_TIME, 'ru_RU.UTF-8');
            $date_start = strftime('%d.%m.%y', strtotime($ticket->date_start));
            $date_end = strftime('%d.%m.%y', strtotime($ticket->date_end));
            $actions = '<a href="/view?id=' . $ticket->id . '"> Просмотреть </a>';
            //$status = self::STATUSES[$ticket->status];
            $tekStatusSogl = DB::table('ticket_status')
                ->where('ticket_id', '=', $ticket->id)
                ->where('roll', TicketStatus::SOGL)
                ->orderBy('created_at', 'desc')
                ->first();

            $tekStatusIspl = DB::table('ticket_status')
                ->where('ticket_id', '=', $ticket->id)
                ->where('roll', TicketStatus::ISPL)
                ->orderBy('created_at', 'desc')
                ->first();
            foreach ($performers as $performer) {
                array_push($performersInfo, [' ' . $performer->first_name . ' ' . $performer->last_name]);
            }

            array_push(
                $ticketsInfo,
                [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'priority' => $priority,
                    'type_task' => $type_task,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'performers' => $performersInfo,
                    'actions' => $actions,
                    'work_status' => ($tekStatusIspl) ? (int)$tekStatusIspl->status : null,
                    'sogl_status' => ($tekStatusSogl) ? (int)$tekStatusSogl->status : null,
                ]
            );

            //обнуляем
            $performersInfo = [];
        }


        if (count($ticketsInfo) == 0) {
            return [];
        }

//        $sort = array();
//        foreach ($ticketsInfo as $k => $v) {
//            $sort['status'][$k] = $v['status'];
//            $sort['date_start'][$k] = $v['date_start'];
//        }
//        array_multisort($sort['status'], SORT_ASC, $sort['date_start'], SORT_ASC, $ticketsInfo);

        return json_encode($ticketsInfo);
    }

    public
    function youTicketsNotAgree(Request $request)
    {

        $findProperty = explode("&", $request->data);
        $findCategory = explode("=", $findProperty[3]);

        $ticketsInfo = [];
        $performersInfo = [];
        $performer_tickets = Reconciliation::where('subdivision_id', '=', Auth::id())->get();
        if ($findCategory[1] > 1) {
            $cat = 1;
        } else {
            $cat = 0;
        }

        //$tickets = Ticket::where('id_user', '=', Auth::id())->get();
        foreach ($performer_tickets as $item) {
            foreach ($item->tickets as $ticket) {
                if ($cat === 0) {
                    $priority = $this->priorities[$ticket->priority] ?? '';
                    $type_task = $this->tasks[$ticket->type_task] ?? '';
                    $performers = $ticket->performer->all();
                    setlocale(LC_TIME, 'ru_RU.UTF-8');
                    $date_start = strftime('%d.%m.%y', strtotime($ticket->date_start));
                    $date_end = strftime('%d.%m.%y', strtotime($ticket->date_end));
                    $actions = '<a href="/view?id=' . $ticket->id . '"> Просмотреть </a>';
                    //$status = self::STATUSES[$ticket->status];
                    $tekStatusSogl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::SOGL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $tekStatusIspl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::ISPL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    foreach ($performers as $performer) {
                        array_push($performersInfo, [' ' . $performer->first_name . ' ' . $performer->last_name]);
                    }

                    array_push(
                        $ticketsInfo,
                        [
                            'id' => $ticket->id,
                            'title' => $ticket->title,
                            'priority' => $priority,
                            'type_task' => $type_task,
                            'date_start' => $date_start,
                            'date_end' => $date_end,
                            'performers' => $performersInfo,
                            'actions' => $actions,
                            'work_status' => ($tekStatusIspl) ? (int)$tekStatusIspl->status : null,
                            'sogl_status' => ($tekStatusSogl) ? (int)$tekStatusSogl->status : null,
                        ]
                    );

                    //обнуляем
                    $performersInfo = [];
                } else if ($cat === 1) {
                    if ($ticket->customer == $findCategory[1])
                        $priority = $this->priorities[$ticket->priority] ?? '';
                    $type_task = $this->tasks[$ticket->type_task] ?? '';
                    $performers = $ticket->performer->all();
                    setlocale(LC_TIME, 'ru_RU.UTF-8');
                    $date_start = strftime('%d.%m.%y', strtotime($ticket->date_start));
                    $date_end = strftime('%d.%m.%y', strtotime($ticket->date_end));
                    $actions = '<a href="/view?id=' . $ticket->id . '"> Просмотреть </a>';
                    //$status = self::STATUSES[$ticket->status];
                    $tekStatusSogl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::SOGL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $tekStatusIspl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::ISPL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    foreach ($performers as $performer) {
                        array_push($performersInfo, [' ' . $performer->first_name . ' ' . $performer->last_name]);
                    }

                    array_push(
                        $ticketsInfo,
                        [
                            'id' => $ticket->id,
                            'title' => $ticket->title,
                            'priority' => $priority,
                            'type_task' => $type_task,
                            'date_start' => $date_start,
                            'date_end' => $date_end,
                            'performers' => $performersInfo,
                            'actions' => $actions,
                            'work_status' => ($tekStatusIspl) ? (int)$tekStatusIspl->status : null,
                            'sogl_status' => ($tekStatusSogl) ? (int)$tekStatusSogl->status : null,
                        ]
                    );

                    //обнуляем
                    $performersInfo = [];

                }

            }
        }

        if (count($ticketsInfo) == 0) {
            return [];
        }

//        $sort = array();
//        foreach ($ticketsInfo as $k => $v) {
//            $sort['status'][$k] = $v['work_status'];
//            $sort['date_start'][$k] = $v['date_start'];
//        }
//        array_multisort($sort['status'], SORT_ASC, $sort['date_start'], SORT_ASC, $ticketsInfo);

        return json_encode($ticketsInfo);
    }

    function youTicketsPerformers(Request $request)
    {

        $findProperty = explode("&", $request->data);
        $findCategory = explode("=", $findProperty[3]);

        $ticketsInfo = [];
        $performersInfo = [];
        $performer_tickets = Performer::where('user_id', '=', Auth::id())->get();
        if ($findCategory[1] > 1) {
            $cat = 1;
        } else {
            $cat = 0;
        }

        //$tickets = Ticket::where('id_user', '=', Auth::id())->get();
        foreach ($performer_tickets as $item) {
            foreach ($item->tickets as $ticket) {
                if ($cat === 0) {
                    $priority = $this->priorities[$ticket->priority] ?? '';
                    $type_task = $this->tasks[$ticket->type_task] ?? '';
                    $performers = $ticket->performer->all();
                    setlocale(LC_TIME, 'ru_RU.UTF-8');
                    $date_start = strftime('%d.%m.%y', strtotime($ticket->date_start));
                    $date_end = strftime('%d.%m.%y', strtotime($ticket->date_end));
                    $actions = '<a href="/view?id=' . $ticket->id . '"> Просмотреть </a>';
                    //$status = self::STATUSES[$ticket->status];
                    $tekStatusSogl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::SOGL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $tekStatusIspl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::ISPL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    foreach ($performers as $performer) {
                        array_push($performersInfo, [' ' . $performer->first_name . ' ' . $performer->last_name]);
                    }

                    array_push(
                        $ticketsInfo,
                        [
                            'id' => $ticket->id,
                            'title' => $ticket->title,
                            'priority' => $priority,
                            'type_task' => $type_task,
                            'date_start' => $date_start,
                            'date_end' => $date_end,
                            'performers' => $performersInfo,
                            'actions' => $actions,
                            'work_status' => ($tekStatusIspl) ? (int)$tekStatusIspl->status : null,
                            'sogl_status' => ($tekStatusSogl) ? (int)$tekStatusSogl->status : null,
                        ]
                    );

                    //обнуляем
                    $performersInfo = [];
                } else if ($cat === 1) {
                    if ($ticket->customer == $findCategory[1])
                        $priority = $this->priorities[$ticket->priority] ?? '';
                    $type_task = $this->tasks[$ticket->type_task] ?? '';
                    $performers = $ticket->performer->all();
                    setlocale(LC_TIME, 'ru_RU.UTF-8');
                    $date_start = strftime('%d.%m.%y', strtotime($ticket->date_start));
                    $date_end = strftime('%d.%m.%y', strtotime($ticket->date_end));
                    $actions = '<a href="/view?id=' . $ticket->id . '"> Просмотреть </a>';
                    //$status = self::STATUSES[$ticket->status];
                    $tekStatusSogl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::SOGL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $tekStatusIspl = DB::table('ticket_status')
                        ->where('ticket_id', '=', $ticket->id)
                        ->where('roll', TicketStatus::ISPL)
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    foreach ($performers as $performer) {
                        array_push($performersInfo, [' ' . $performer->first_name . ' ' . $performer->last_name]);
                    }

                    array_push(
                        $ticketsInfo,
                        [
                            'id' => $ticket->id,
                            'title' => $ticket->title,
                            'priority' => $priority,
                            'type_task' => $type_task,
                            'date_start' => $date_start,
                            'date_end' => $date_end,
                            'performers' => $performersInfo,
                            'actions' => $actions,
                            'work_status' => ($tekStatusIspl) ? (int)$tekStatusIspl->status : null,
                            'sogl_status' => ($tekStatusSogl) ? (int)$tekStatusSogl->status : null,
                        ]
                    );

                    //обнуляем
                    $performersInfo = [];

                }

            }
        }

        if (count($ticketsInfo) == 0) {
            return [];
        }

//        $sort = array();
//        foreach ($ticketsInfo as $k => $v) {
//            $sort['status'][$k] = $v['work_status'];
//            $sort['date_start'][$k] = $v['date_start'];
//        }
//        array_multisort($sort['status'], SORT_ASC, $sort['date_start'], SORT_ASC, $ticketsInfo);

        return json_encode($ticketsInfo);
    }

    public
    function auth(Request $request)
    {
        try {

            $user = User::where('email', '=', $request->email . '@eurasia.com')->first();

            if ($user) {
                if (Hash::check($request->password, $user['password'])) {
                    Auth::login($user);
                    return redirect('/');
                }
            } else {

                $result = $this->ticket1C->getUser1C(['login' => $request->email, 'password' => $request->password]);

                if ($result) {
                    $user = User::create([
                        'name' => $request->email,
                        'email' => $request->email . '@eurasia.com',
                        'password' => Hash::make($request->password),
                    ]);
                    Auth::login($user);
                } else {
                    return redirect('/error_auth_1c');
                }
            }

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public
    function errorAuth1C()
    {
        return view('error.errorAuth1C');
    }

    public
    function migrationUserc1C()
    {
        $result = $this->ticket1C->getListAccounts1C();

        foreach ($result->List as $key => $value) {
            $domain = '';
            $l = '';
            if (isset($value->Domain)) {
                $domain = $value->Domain;
                $str = str_replace('\\', ' ', $value->Domain);
                $login = explode(' ', $str);
                if (isset($login[3])) {
                    $l = $login[3];
                }
            }
            $fio = explode(' ', $value->FIO);
            $o = '';
            if (isset($fio[2])) {
                $o = $fio[2];
            }
            PeopleJob::create(['login' => $l, 'fio' => $value->FIO, 'position' => $value->Position, 'domain' => $domain, 'unit' => $value->Unit, 'f' => $fio[0], 'i' => $fio[1], 'o' => $o]);
        }
    }

    public
    function addStatusIspl(Request $request)
    {
        $isplStatus = (int)$request->ispl;
        $id = (int)$request->id;
        if ($isplStatus != "null") {
            $newStatusIspl = [
                'user_id' => Auth::id(),
                'roll' => TicketStatus::ISPL,
                'status' => $isplStatus,
                'ticket_id' => $id,
            ];
            TicketStatus::create($newStatusIspl);
        }

    }

    public
    function addStatusSogl(Request $request)
    {
        $soglStatus = (int)$request->sogl;
        $id = (int)$request->id;

        if ((int)$soglStatus != "null") {
            $newStatusSogl = [
                'user_id' => Auth::id(),
                'roll' => TicketStatus::SOGL,
                'status' => $soglStatus,
                'ticket_id' => $id,
            ];
            TicketStatus::create($newStatusSogl);
        }

    }

    public
    function addStatusZakaz(Request $request)
    {
        $soglStatus = (int)$request->zakaz;
        $id = (int)$request->id;

        if ((int)$soglStatus != "null") {
            $newStatusZakaz = [
                'user_id' => Auth::id(),
                'roll' => TicketStatus::ZAKA,
                'status' => $soglStatus,
                'ticket_id' => $id,
            ];
            TicketStatus::create($newStatusZakaz);
        }

    }

    /*
     * получаем всех исполнителей и их задачи/согласования
     */

    public
    function allPeopleWork(Request $request)
    {
        $user = User::all();

        foreach ($user as $key => $item) {
            $i = count($item->performers);
            $user->performer = $i;
        }
        foreach ($user as $key => $item) {
            $i = count($item->reconciliations);
            $user->reconciliations = $i;
        }
        return $user;
    }

    public
    function showPeopleWork(Request $request)
    {
        return view('people.tickets', ['user_id' => $request->id]);
    }

    /*
     * Получение задач на исполнение
     */
    public
    function dataPeopleWork(Request $request)
    {

        $user = User::where('id', '=', $request->user_id)->first();

        $all = [];

        foreach ($user->performers as $item) {
            $ticket = Ticket::where('id','=',$item['ticket_id'])->first()->toArray();

            $tekStatusIspl = DB::table('ticket_status')
                ->where('ticket_id', '=', $item['ticket_id'])
                ->orderBy('created_at', 'desc')
                ->first();
            $ticket['work_status'] = ($tekStatusIspl) ? (int)$tekStatusIspl->status : null;
            array_push($all, $ticket);
        }

        return $all;
    }

    /*
     * Получение задач на согласование
     */
    public
    function dataPeopleReconciliations(Request $request)
    {

        $user = User::where('id', '=', $request->user_id)->first();

        $all = [];

        foreach ($user->reconciliations as $item) {
            $ticket = Ticket::where('id','=',$item['ticket_id'])->first()->toArray();
            $tekStatusIspl = DB::table('ticket_status')
                ->where('ticket_id', '=', $item['ticket_id'])
                ->orderBy('created_at', 'desc')
                ->first();
            $ticket['sogl_status'] = ($tekStatusIspl) ? (int)$tekStatusIspl->status : null;
            array_push($all, $ticket);
        }

        return $all;
    }
}
