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
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>
    <body class="antialiased">
        <div class="flex flex-row">
            <div class="user-panel absolute inset-x-0 top-0 text-center">
                <div class="relative h-full">
                    <div class="app-logo-chat w-full text-center text-white text-5xl">
                        Czatap
                    </div>
                    <i class="fas fa-bars" id="toggle-menu"></i>
                </div>
                <div class="flex flex-col h-screen text-center items-center" id="user-dashboard">
                    <i class="fas fa-times" id="close-menu"></i>
                    <!-- Profile logo -->
                    <img src="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" class="profile-image mt-8 mb-4"/>
                    <div class="hello-user w-full text-center mb-8">
                        Witaj {{ $user->nick }}
                    </div>
                    <!-- Btns -->
                    <div class="btns-box flex-grow flex flex-col mb-8">
                        <button class="btn my-4" id="openFriends"><i class="fas fa-users"></i> Znajomi</button>
                        <button class="btn my-4" id="openConversations"><i class="far fa-comment"></i> Konwersacje</button>
                        <button class="btn my-4" id="openSettings"><i class="fas fa-cogs"></i> Ustawienia</button>
                        <a href="/logout" class="btn my-4 logout"><i class="fas fa-sign-out-alt"></i> Wyloguj się</a>
                    </div>
                </div>
            </div>
            <div class="content-panel flex flex-col flex-grow h-screen">
                {!! $content !!}
            </div>
        </div>
        <div class="full-shadow w-screen h-screen absolute top-0 left-0" style="display:none;"></div>

        <div id="settingsModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
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
        </div>

        <div id="friendsModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
            <div class="modal-title w-full text-center relative">Znajomi
                <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
            </div>
            <div class="w-full text-center mt-6">Wpisz nick i dodaj znajomego</div>
            <form class="flex flex-row justify-around mt-2" id="add_friend_form" method="POST">
                @csrf
                <input class="form-input mb-4 block w-3/6 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="nickname" id="nickname" placeholder="nickname"/>
                <button class="cta-btn form-submit box-content rounded-xl" id="add_friend" type="button">Dodaj <i class="fas fa-paper-plane"></i></button>
            </form>
            <div class="list flex flex-row">
                @foreach ($friends as $friend)
                    <div class="friend relative flex flex-row flex-wrap">
                        <div class="profile_container relative flex flex-row justify-center align-center items-center">
                            <img src="{{ asset('storage/profiles_miniatures/'.$friends_data[$friend['id']]['profile_img']) }}" class="profile-image"/>
                            @if ($friends_data[$friend['id']]['status'] == 0)
                                <i class="fas fa-user-clock waiting_friend"></i>
                            @endif
                        </div>
                        <div class="friend_name ml-2">{{ $friends_data[$friend['id']]['nick'] }}</div>
                        <i class="friend_name fas fa-ellipsis-v open_fast_menu ml-4" data="{{ $friend['id'] }}"></i>
                        <div class="fast_menu absolute flex flex-col" style="display: none;">
                            @if ($friends_data[$friend['id']]['status'] == 0 && $friends_data[$friend['id']]['invite'] == 1)
                                <!-- Accept/Deceline menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Zaproszenie</div>
                                <button class="fast_menu_btn mb-2 w-full" id="acceptInvite" data="{{ $friend['id'] }}"><i class="fas fa-user-check"></i> Akceptuj</button>
                                <button class="fast_menu_btn mb-2 w-full" id="decelineInvite" data="{{ $friend['id'] }}"><i class="fas fa-user-minus"></i> Odrzuć</button>
                                <button class="fast_menu_btn mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>

                            @elseif ($friends_data[$friend['id']]['status'] == 0 && $friends_data[$friend['id']]['invite'] == 0)
                                <!-- Cancel invite menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Zaproszenie</div>
                                <button class="fast_menu_btn mb-2 w-full" id="cancelInvite" data="{{ $friend['id'] }}"><i class="fas fa-user-slash"></i> Anuluj</button>
                                <button class="fast_menu_btn mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @elseif ($friends_data[$friend['id']]['status'] == 1)
                                <!-- Friendship menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="fas fa-user-friends"></i> Znajomość</div>
                                <button class="fast_menu_btn mb-2 w-full" id="blockFriendship" data="{{ $friend['id'] }}"><i class="fas fa-comment-slash"></i> Zablokuj</button>
                                <button class="fast_menu_btn mb-2 w-full" id="deleteFriendship" data="{{ $friend['id'] }}"><i class="fas fa-user-slash"></i> Usuń</button>
                                <button class="fast_menu_btn mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}"/>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <audio style="display:none;" id="notifySound">
            <source src="{{ asset('storage/sounds/mmm-2-tone-sexy.mp3') }}" type="audio/mpeg">
        </audio> 
        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="/js/app.js"></script>
    </body>
</html>
