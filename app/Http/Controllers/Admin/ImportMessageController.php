<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportMessage;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ImportMessageController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $messages = ImportMessage::get();

            return DataTables::of($messages)
                ->addColumn('date',function ($q){
                   $date=Carbon::parse($q->created_at)->format('d-m-Y-H-i-s');
                   return $date;
                })
                ->addColumn('sender_name',function ($q){
                    if ($q->created_by!=null) {
                        $sender=User::where('id',$q->created_by)->first();
                        return $sender->firstName.' '.$sender->lastName;
                    }else{
                        return 'N/A';
                    }
                })->addColumn('receive_name',function ($q){
                    if ($q->received_by != null) {
                        $sender=User::where('id',$q->received_by)->first();
                        return $sender->firstName.' '.$sender->lastName;
                    }else{
                        return 'N/A';
                    }
                })
                ->addColumn('is_read',function ($q){
                    return $q->is_read ? '<span class="badge sm badge-primary">Read</span>' : '<span class="badge sm badge-danger">Unread</span>';
                })
                ->addColumn('ticket_number',function ($q){
                //  $ticket=Ticket::where('id',$q->ticket_id)->first();
                 return 'test';
                })
                   ->addColumn('actions', function ($row) {
                    $btn = '<button type="button" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['date','ticket_number','sender_name','is_read','receive_name','actions'])
//                ->with([
//                    'totalAmount' => $totalAmount,
//                    'totalCount' => $totalCount,
//                ])
                ->make(true);
        }
        return view('admin.import_messages.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            // 'ticket_id' => 'required|exists:tickets,id',
            'message' => 'required|string',
//            'received_by' => 'required|string',
        ]);

        $message = ImportMessage::create([
            'user_id' => Auth::id(),
            // 'ticket_id' => $request->ticket_id,
            'message' => $request->message,
            'created_by' => Auth::id(),
            'received_by' => null,
            'is_read' => false,
        ]);

        return response()->json($message);
    }

    public function getUnreadMessages()
    {
        $messages = ImportMessage::where('is_read', false)
            ->with('ticket')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    // 'ticket_number' => $message->ticket->ticket_number ?? 'Unknown Ticket'
                ];
            });

        return response()->json($messages);
    }
    public function markMessageAsRead(Request $request)
    {
        ImportMessage::where('id', $request->id)
            ->update(['is_read' => true,
                'received_by'=>Auth::id(),
            ]);
        return response()->json(['status' => 'success']);
    }
    public function markAllMessagesAsRead()
    {
        ImportMessage::where('is_read', false)->update(['is_read' => true,'received_by'=>Auth::id()]);
        return response()->json(['status' => 'success']);
    }
    public function destroy($id){
        $message=ImportMessage::findorFail($id);
        if ($message) {
            $message->delete();
            return response()->json(['message' => 'Message deleted successfully!']);
        }
        return response()->json(['message' => 'Message not found!'], 404);

    }

}
