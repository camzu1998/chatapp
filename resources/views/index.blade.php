<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <title>Czatap</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&family=Rampart+One&display=swap" rel="stylesheet">  
        
        <script src="https://kit.fontawesome.com/309a8b3aa5.js" crossorigin="anonymous"></script>
    </head>
    <body class="antialiased">
        <div class="flex flex-row">
            <div class="user-panel flex flex-col h-screen text-center items-center">
                <!-- Profile logo -->
                <img src="{{ asset('storage/profiles_miniatures/no_image.jpg') }}" class="profile-image mt-8 mb-4"/>
                <div class="hello-user w-full text-center mb-8">
                    Witaj {{ $user->nick }}
                </div>
                <!-- Btns -->
                <div class="btns-box flex-grow flex flex-col mb-8">
                    <button class="btn my-4 disabled" id="openConversations" disabled><i class="far fa-comment"></i> Konwersacje</button>
                    <button class="btn my-4" id="openSettings"><i class="fas fa-cogs"></i> Ustawienia</button>
                    <a href="/logout" class="btn my-4 logout"><i class="fas fa-sign-out-alt"></i> Wyloguj się</a>
                </div>
            </div>
            <div class="content-panel flex flex-col flex-grow h-screen">
                <!-- Messages -->
                <div id="messagesList" class="w-full flex flex-col text-gray-200 px-12 pt-8" style="height: calc(100vh - 24px); overflow-y: auto;">
                    @foreach ($messages as $msg)
                        @if ($msg->user_id != $user->id)
                            <div class="msg msg-left mb-12 relative p-2">
                        @elseif ($msg->user_id == $user->id)
                            <div class="msg msg-right mb-12 relative p-2">
                        @endif
                                <img src="{{ asset('storage/profiles_miniatures/no_image.jpg') }}" class="msg-image absolute"/>
                                <div class="msg-content">
                                    <span class="msg-user_name">{{ $msg->nick }}</span>
                                    <p class="msg-content-p" >{{ $msg->content }}</p>
                                    <span class="msg-date"></span>
                                    @if ($msg->file_id != 0)
                                        <p class="msg-file"> <a href="{{ asset('storage/'.$files[$msg->file_id][0]->path) }}"> {{ $files[$msg->file_id][0]->filename }} </a> </p> 
                                    @endif
                                </div>
                            </div>
                        
                    @endforeach
                </div>
                <!-- Message Form -->
                <div class="flex flex-row fixed bottom-0 right-0" style="width: calc( 100% - 430px );">
                    <form class="w-full flex flex-row" id="msgForm" enctype='multipart/form-data'>
                        @csrf
                        <input type="hidden" name="nick" id="nick" placeholder="Nick" value="{{ $user->nick }}"/>
                        <textarea class="w-10/12" name="content" id="content" placeholder="Napisz wiadomość..."></textarea>
                        <div class="flex flex-col flex-grow">
                            <button class="cta-btn msg-submit" id="send">Wyślij <i class="far fa-save"></i></button>
                            <label for="file" class="form-input file-input text-center">Wgraj plik <i class="fas fa-upload"></i></label>
                            <input type="file" name="file" id="file" style="display: none;"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="full-shadow w-screen h-screen"></div>
        <div class="settingsModal flex flex-col absolute">
    
        </div>
        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script>
            $('#send').click(function(){
                var user_id = {{ $user->id }};
                var fd = new FormData();
                var files = $('#file')[0].files;
                
                // Check file selected or not
                if(files.length > 0 )
                    fd.append('file',files[0]);

                fd.append('_token', '{{ csrf_token() }}');
                fd.append('nick', $('#nick').val());
                fd.append('content', $('#content').val());

                $.ajax({
                    method: 'post',
                    url: '/send_msg',
                    data: fd,
                    contentType: false,
                    processData: false
                }).always(function(res){
                    var html = '';
                    for(var i = 0; i < res.messages.length; i++){
                        var msg = res.messages[i];
                        var file_html = '';
                        if(msg.file_id != 0){
                            var file = res.files[msg.file_id][0];
                            file_html = '<p class="w-full"> <a href="/storage/'+file.path+'"> '+file.filename+' </a> </p> ';
                        }
                        if(user_id == msg.user_id){
                            html += '<div class="msg msg-right mb-12 relative p-2">';
                        }else{
                            html += '<div class="msg msg-left mb-12 relative p-2">';
                        }
                        html += '<img src="http://localhost/storage/profiles_miniatures/no_image.jpg" class="msg-image absolute"/><div class="msg-content"><span class="msg-user_name">'+msg.nick+'</span><p class="msg-content-p" >'+msg.content+'</p><span class="msg-date"></span>'+file_html+'</div></div>';
                    }
                    $('#messagesList').html(html);
                });

                $('#content').val('');
                $('#file').val('');
                return false;
            });
            setInterval(function(){
                var user_id = {{ $user->id }};
                $.ajax({
                    method: 'post',
                    url:    '/get_msg',
                    data:   {_token: '{{ csrf_token() }}'}
                }).always(function(res){
                    var html = '';
                    for(var i = 0; i < res.messages.length; i++){
                        var msg = res.messages[i];
                        var file_html = '';
                        if(msg.file_id != 0){
                            var file = res.files[msg.file_id][0];
                            file_html = '<p class="w-full"> <a href="/storage/'+file.path+'"> '+file.filename+' </a> </p> ';
                        }

                        if(user_id == msg.user_id){
                            html += '<div class="msg msg-right mb-12 relative p-2">';
                        }else{
                            html += '<div class="msg msg-left mb-12 relative p-2">';
                        }
                        html += '<img src="http://localhost/storage/profiles_miniatures/no_image.jpg" class="msg-image absolute"/><div class="msg-content"><span class="msg-user_name">'+msg.nick+'</span><p class="msg-content-p" >'+msg.content+'</p><span class="msg-date"></span></div>'+file_html+'</div>';
                    }
                    $('#messagesList').html(html);
                });
                
                return false;
            }, 3000);
        </script>
    </body>
</html>
