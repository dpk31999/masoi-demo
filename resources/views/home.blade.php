@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    {{Session::get('error')}}
                </div>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{Session::get('success')}}
                </div>
            @endif
            <div class="card">
                <div class="card-header">Phòng chơi</div>
            </div>
            <div class="row mt-5" id="room1">
                @foreach ($rooms as $room)
                    <div class="col-md-3">
                        <a href="{{route('room',$room->id)}}" class="text-decoration-none text-dark">
                            @if($room->count >= 12)
                                <div id="alert" class="alert alert-warning" role="alert">
                            @else
                                <div id="alert" class="alert alert-info">
                            @endif
                                <h1>Phòng {{$room->number}}</h1>
                                <p>Số người chơi:<span id="count{{$room->id}}">{{$room->count}}</span>/12</p>
                                <p id="status{{$room->id}}">Trạng thái: {{$room->status}}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
