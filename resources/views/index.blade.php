<!DOCTYPE html>
<html lang="pl">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <title>Czatap</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="icon" type="image/png" sizes="192x192"  href="/storage/images/android-icon-192x192.png">

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&family=Rampart+One&display=swap" rel="stylesheet">  
        
        <script src="https://kit.fontawesome.com/309a8b3aa5.js" crossorigin="anonymous"></script>
        <script>
            navigator.serviceWorker.register('/sw.js').catch(e=>console.error('Ups!' + e))
        </script>
        <link rel="manifest" href="/manifest.json">
    </head>
    <body class="antialiased">
        <div class="flex flex-row">
            <div class="user-panel flex flex-col h-screen text-center items-center">
                <!-- Profile logo -->
                <img src="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" class="profile-image mt-8 mb-4"/>
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
                <div id="messagesList" class="w-full flex flex-col text-gray-200 px-12 pt-8" style="height: calc(100vh - 100px); overflow-y: auto;">
                    @foreach ($messages as $msg)
                        @if ($msg->user_id != $user->id)
                            <div class="msg msg-left mb-12 relative p-2">
                        @elseif ($msg->user_id == $user->id)
                            <div class="msg msg-right mb-12 relative p-2">
                        @endif
                                <img src="{{ asset('storage/profiles_miniatures/'.$msg_users[$msg->user_id]['profile_img']) }}" class="msg-image absolute"/>
                                <div class="msg-content">
                                    <span class="msg-user_name">{{ $msg->nick }}</span>
                                    <p class="msg-content-p" >{{ $msg->content }}</p>
                                    <span class="msg-date"></span>
                                    @if ($msg->file_id != 0)
                                        <p class="msg-file"> <a href="{{ asset('storage/'.$files[$msg->file_id][0]->path) }}"><i class="far fa-file"></i> {{ $files[$msg->file_id][0]->filename }} </a> </p> 
                                    @endif
                                </div>
                            </div>
                        
                    @endforeach
                </div>
                <!-- Message Form -->
                <div class="flex flex-row fixed bottom-0 right-0 formContainer">
                    <form class="w-full flex flex-row" id="msgForm" enctype='multipart/form-data'>
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
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
        <div class="full-shadow w-screen h-screen absolute top-0 left-0" style="display:none;"></div>
        <div id="settingsModal" class="flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
            <div class="modal-title w-full text-center relative">Ustawienia
                <span class="close absolute top-0 left-full">X</span>
            </div>
            <form class="flex flex-row">
                <div class="w-6/12 flex flex-col justify-center items-center">
                    <img src="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" class="profile-image mt-8 mb-4"/>
                    <label for="input_profile" class="file-input text-center box-content rounded-xl px-2">Wgraj nowe zdjęcie <i class="fas fa-upload"></i></label>
                    <input type="file" name="input_profile" id="input_profile" style="display: none;"/>
                </div>
                <div class="w-6/12 flex flex-col mt-8">
                    <div class="form-group flex flex-row mb-3 ml-3">
                        <label class="switch">
                            <input type="checkbox" name="sounds" id="sounds" value="1">
                            <span class="slider round"></span>
                        </label>
                        <span class="label">Dźwięki są wyłączone</span>
                    </div>
                    <div class="form-group flex flex-row mb-3 ml-3">
                        <label class="switch">
                            <input type="checkbox" name="notifications" id="notifications" value="1">
                            <span class="slider round"></span>
                        </label>
                        <span class="label">Powiadomienia są wyłączone</span>
                    </div>
                    <div class="form-group flex flex-row mb-3 ml-3">
                        <label class="switch">
                            <input type="checkbox" name="press_on_enter" id="press_on_enter" value="1">
                            <span class="slider round"></span>
                        </label>
                        <span class="label">Wyślij na [Enter] jest wyłączone</span>
                    </div>
                </div>
            </form>
            <div class="w-full h-full flex flex-row justify-end items-end">
                <button class="cta-btn form-submit box-content rounded-xl" id="save_settings">Zapisz <i class="far fa-save"></i></button>
            </div>
            <audio style="display:none;" id="notifySound">
                <source src="{{ asset('storage/sounds/mmm-2-tone-sexy.mp3') }}" type="audio/mpeg">
            </audio> 
        </div>
        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}"/>
        <input type="hidden" name="newest_id" id="newest_id" value="{{ $newest_msg }}"/>
        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="/js/app.js"></script>
    </body>
</html>
