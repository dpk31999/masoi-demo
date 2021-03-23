<?php

namespace App\Http\Controllers;

use App\Room;
use App\Message;
use Pusher\Pusher;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Room $room,Request $request)
    {
        $message = Message::create([
            'user_id' => auth()->user()->id,
            'room_id' => $room->id,
            'message' => $request->message,
            'type' => $request->type
        ]);
        
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('channel-send-message', 'event-send-message', [
            'room' => $room,
            'user' => auth()->user(),
            'message' => $message
        ]);

        return response()->json([
            'room' => $room,
        ], 200);
    }

    public function getByType(Room $room,$type)
    {
        $messages = Message::where([
            'room_id' => $room->id,
            'type' => $type
        ])->get();

        foreach($messages as $message)
        {
            $message->user_name = $message->user->name;
        }

        return response()->json([
            'messages' => $messages
        ], 200);
    }
}
