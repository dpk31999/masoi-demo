@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex d-flex justify-content-between">
        <a href="{{route('outroom',$room->id)}}" class="btn btn-outline-secondary mb-2">Rời phòng</a>
        <button class="btn btn-outline-secondary mb-2">Bắt đầu game (tối thiếu 8 người)</button>
        <button class="btn btn-outline-primary mb-2">0s</button>
        <button class="btn btn-outline-primary mb-2" data-toggle="tooltip" data-placement="bottom" title="Nhiệm vụ là tìm ra người tin tưởng để chọn trưởng làng để đưa đến chiến thắng">Dân đen</button>
    </div>
    <div class="card">
        <div class="card-header">Phòng chơi số {{$room->number}}</div>
    </div>
    <div class="row mt-2">
        <div class="col-md-8">
            <div class="row" id="users">
                @foreach ($room->users as $user)
                    <div class="col-md-3" id="user{{$user->id}}">
                        <div class="alert alert-info">
                            <h3>{{$user->name}}</h3>
                            <p>Role : chưa biết</p>
                            <p>Status: Còn sống</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-3" id="chat{{$room->id}}" style="background-color: #d0f7f2">
            <div id="chatBorder" class="villager" style="height: 350px;overflow-y: scroll;position: relative;">
                @foreach ($messages as $message)
                    @if ($message->user->id == auth()->user()->id)
                        <p style="color: red">{{$message->user->name}}: {{$message->message}}</p>
                    @else 
                        <p>{{$message->user->name}}: {{$message->message}}</p>
                    @endif
                @endforeach
            </div>
            <div style="display: flex" class="mb-2">
                <img class="icon border icon-villager" data-id="{{$room->id}}" src="https://img.icons8.com/ios-glyphs/64/000000/human-head.png" width="30" height="30" style="cursor: pointer;"/>
                <img class="icon icon-wolf" data-id="{{$room->id}}" src="https://img.icons8.com/ios-filled/30/000000/wolf.png" width="30" height="30" style="cursor: pointer;"/>
                <img class="icon icon-love" data-id="{{$room->id}}" src="https://img.icons8.com/bubbles/50/000000/like.png" width="30" height="30" style="cursor: pointer;"/>
            </div>
            <form class="form-message" action="{{route('message.store',$room->id)}}" method="POST">
                @csrf   
                <input type="hidden" id="type" name="type" value="villager">
                <div class="form-group">
                    <input type="text" class="form-control" name="message" id="message{{$room->id}}" placeholder="Enter message">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection