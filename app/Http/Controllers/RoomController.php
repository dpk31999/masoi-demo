<?php

namespace App\Http\Controllers;

use App\Room;
use App\Message;
use Pusher\Pusher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index($id)
    {
        $room = Room::findOrFail($id);

        // check room is full ?
        if($room->count >= 12)
        {
            session()->flash('error','Phòng đã đầy vui lòng chọn phòng khác hoặc chờ!'); 
            return redirect()->route('home');
        }

        $messages = Message::where([
            'room_id' => $room->id,
            'type' => 'villager'
        ])->get();

        // check user exist in room
        if(auth()->user()->rooms->contains('id',$room->id))
        {
            return view('room',\compact('room','messages'));
        }

        $room->count += 1;
        $room->save();

        $room->users()->attach(auth()->user()->id);

        // send data to pusher
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

        $pusher->trigger('channel-join-room', 'event-join-room', [
            'room' => $room,
            'name_user' => auth()->user()->name,
            'id_user' => auth()->user()->id
        ]);

        return view('room',\compact('room','messages'));
    }

    public function outRoom($id)
    {
        $room = Room::findOrFail($id);

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

        $pusher->trigger('channel-out-room', 'event-out-room', [
            'room' => $room,
            'name_user' => auth()->user()->name,
            'id_user' => auth()->user()->id
        ]);

        $room->count -= 1;
        $room->save();

        if($room->count == 0)
        {
            Message::where('room_id',$room->id)->delete();
        }

        $room->users()->detach(auth()->user()->id);

        session()->flash('success','Đã rời phòng số '. $room->number .'!'); 
        return redirect()->route('home');
    }
}
