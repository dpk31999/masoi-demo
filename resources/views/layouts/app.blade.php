<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">Ma Sói Online</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js" integrity="sha512-v8ng/uGxkge3d1IJuEo6dJP8JViyvms0cly9pnbfRxT6/31c3dRWxIiwGnMSWwZjHKOuY3EVmijs7k1jz/9bLA==" crossorigin="anonymous"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function(){
            // Enable pusher logging - don't include this in production
            var my_id = "{{ Auth::id() }}";
            Pusher.logToConsole = true;
    
            var pusher = new Pusher('c519f7b15c369aec16f9', {
            cluster: 'ap1'
            });

            var channelJoinRoom = pusher.subscribe('channel-join-room');

            channelJoinRoom.bind('event-join-room', function(data) {
                var count = parseInt($('#count' + data.room.id).html());
                count++;
                $('#count' + data.room.id).html(count);

                if(count == 12)
                {
                    $('#alert').removeClass("alert-info");
                    $('#alert').addClass("alert-warning");
                }

                $('#users').append('<div class="col-md-3" id="user'+ data.id_user +'">' + 
                                        '<div class="alert alert-info">' +
                                            '<h3>'+ data.name_user +'</h3>' +
                                            '<p>Role : chưa biết</p>' +
                                            '<p>Status: Còn sống</p>' +
                                        '</div>' +
                                    '</div>'
                                    );
            });

            var channelOutRoom = pusher.subscribe('channel-out-room');

            channelOutRoom.bind('event-out-room', function(data) {
                var count = parseInt($('#count' + data.room.id).html());
                count--;

                $('#count' + data.room.id).html(count);

                if(count == 11)
                {
                    $('#alert').removeClass("alert-warning");
                    $('#alert').addClass("alert-info");
                }

                $('#user' + data.id_user).remove();
            });

            var channelSendMessage = pusher.subscribe('channel-send-message');

            channelSendMessage.bind('event-send-message', function(data) {
                if(data.user.id == my_id)
                {
                    $('#chat' + data.room.id + ' ' + '.' + data.message.type).append('<p style="color:red">'+ data.user.name + ': ' + data.message.message +'</p>');
                }
                else
                {
                    $('#chat' + data.room.id + ' ' + '.' + data.message.type).append('<p>'+ data.user.name + ': ' + data.message.message +'</p>');
                }
            });

            $('.icon-villager').on('click',function(){
                $('.icon').removeClass('border');
                $('#chatBorder').removeClass();
                $('#chatBorder').addClass('villager');
                $(this).addClass('border');
                var id_room = $(this).data('id');
                $('#type').val('villager');
                $('#chatBorder').html('<div class="spinner-border text-dark" role="status" style="position: absolute;left: 50%;top: 50%;"><span class="sr-only">Loading...</span></div>');
                $.ajax({
                    type: 'GET',
                    url: '/room/' + id_room + '/type/villager',
                    success: function(data){
                        $('#chatBorder').html('');
                        data.messages.forEach(message => {
                            if(message.user_id == my_id)
                            {
                                $('#chatBorder').append('<p style="color:red">'+ message.user_name + ': ' + message.message +'</p>')
                            }
                            else
                            {
                                $('#chatBorder').append('<p>'+ message.user_name + ': ' + message.message +'</p>')
                            }
                        });
                    }
                });
            });

            $('.icon-wolf').on('click',function(){
                $('.icon').removeClass('border');
                $('#chatBorder').removeClass();
                var id_room = $(this).data('id');
                $('#chatBorder').addClass('wolf');
                $(this).addClass('border');
                $('#type').val('wolf');
                $('#chatBorder').html('<div class="spinner-border text-dark" role="status" style="position: absolute;left: 50%;top: 50%;"><span class="sr-only">Loading...</span></div>');
                $.ajax({
                    type: 'GET',
                    url: '/room/' + id_room + '/type/wolf',
                    success: function(data){
                        $('#chatBorder').html('');
                        data.messages.forEach(message => {
                            if(message.user_id == my_id)
                            {
                                $('#chatBorder').append('<p style="color:red">'+ message.user_name + ': ' + message.message +'</p>')
                            }
                            else
                            {
                                $('#chatBorder').append('<p>'+ message.user_name + ': ' + message.message +'</p>')
                            }
                        });
                    }
                });
            });

            $('.icon-love').on('click',function(){
                $('.icon').removeClass('border');
                $('#chatBorder').removeClass();
                var id_room = $(this).data('id');
                $('#chatBorder').addClass('love');
                $(this).addClass('border');
                $('#type').val('love');
                $('#chatBorder').html('<div class="spinner-border text-dark" role="status" style="position: absolute;left: 50%;top: 50%;"><span class="sr-only">Loading...</span></div>');
                $.ajax({
                    type: 'GET',
                    url: '/room/' + id_room + '/type/love',
                    success: function(data){
                        $('#chatBorder').html('');
                        data.messages.forEach(message => {
                            if(message.user_id == my_id)
                            {
                                $('#chatBorder').append('<p style="color:red">'+ message.user_name + ': ' + message.message +'</p>')
                            }
                            else
                            {
                                $('#chatBorder').append('<p>'+ message.user_name + ': ' + message.message +'</p>')
                            }
                        });
                    }
                });
            });

            $('.form-message').submit(function(e){
                $form = $(this); //wrap this in jQuery
                var route = $form.attr('action');
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: route,
                    data: $(this).serialize(),
                    success: function(data){
                        $('#message' + data.room.id).val('');
                    }
                });
            });
        });
    </script>
</body>
</html>
