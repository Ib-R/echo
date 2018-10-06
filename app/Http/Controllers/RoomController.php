<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Message;
use App\Events\NewMessage;

class RoomController extends Controller
{
    public function index($id)
    {
        return view('room',['id'=>$id]);
    }

    public function show($id)
    {
        $data = Message::where('room_id',$id)
        ->join('users','messages.user_id','=','users.id')
        ->select('body','name','messages.created_at')
        ->get();
        return $data->toJson();
    }

    public function store(Request $request,$id)
    {
        $msg = new Message;
        $msg->body = $request->body;
        $msg->user_id = Auth::id();
        $msg->room_id = $id;
        $msg->save();
        
        $data = Message::where('room_id',$id)
        ->join('users','messages.user_id','=','users.id')
        ->select('body','name','messages.created_at')
        ->orderBy('messages.created_at', 'desc')->first();

        broadcast(new NewMessage($msg))->toOthers();
        return $data->toJson();
    }
}
