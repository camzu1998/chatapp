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
        <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
        <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&family=Rampart+One&display=swap" rel="stylesheet">  
        
        <script src="https://kit.fontawesome.com/309a8b3aa5.js" crossorigin="anonymous"></script>
        <script>
            navigator.serviceWorker.register('/sw.js').catch(e=>console.error('Ups!' + e))
        </script>
        <link rel="manifest" href="/manifest.json">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>
    <body class="antialiased overflow-x-hidden">
        <div class="flex flex-row">
            <div class="user-panel absolute inset-x-0 top-0 text-center">
                <div class="relative h-full">
                    <a class="app-logo-chat block w-full text-center text-white text-5xl" href="/main">
                        Czatap
                    </a>
                    <i class="fas fa-bars" id="toggle-menu"></i>
                </div>
                <div class="flex flex-col h-screen text-center items-center" id="user-dashboard">
                    <i class="fas fa-times" id="close-menu"></i>
                    <!-- Profile logo -->
                    <img src="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" class="profile-image md:block hidden mt-8 mb-4"/>
                    <div class="hello-user  md:w-full md:text-center md:mb-8">
                    Witaj {{ $user->nick }}
                        @if($room_id != 0)
                            w pokoju {{ $room->room_name }}
                        @endif
                    </div>
                    <!-- Btns -->
                    <div class="btns-box flex-grow flex flex-col w-full"> 
                        @if( $room_id != 0 && $rooms_data[$room_id]->admin_id == $user->id )
                            <button class="btn animated-button my-1 md:my-8 modalToggle btn-secondary" data="roomSettingsModal"><span class="relative w-full text-center z-10"><i class="fas fa-sliders-h"></i> Ustawienia pokoju</span></button>
                        @endif
                        <button class="btn animated-button victoria-three my-1 md:my-4 w-full modalToggle" data="friendsModal"><span class="relative w-full text-center z-10"><i class="fas fa-users"></i> Znajomi</span></button>
                        <button class="btn animated-button victoria-three my-1 md:my-4 w-full modalToggle" data="roomsModal" id="roomsModalBtn">
                            <span class="relative w-full text-center z-10"><i class="far fa-comment"></i> Pokoje</span>
                            <span class="absolute right-2 top-1/2 text-center z-10 btn-notify" style="display:none;"></span>
                        </button>
                        <button class="btn animated-button victoria-three my-1 md:my-4 w-full modalToggle" data="settingsModal"><span class="relative w-full text-center z-10"><i class="fas fa-cogs"></i> Ustawienia</span></button>
                        @if($room_id != 0)
                            <a href="/" class="btn animated-button btn-gray my-1 md:my-8 w-full"><span class="relative w-full text-center z-10"><i class="fas fa-arrow-left"></i> Wróc na stronę główną</span></a>
                        @endif
                        <a href="/logout" class="btn animated-button my-1 md:mt-4 w-full logout"><span class="relative w-full text-center z-10"><i class="fas fa-sign-out-alt"></i> Wyloguj się</span></a>
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
                <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
            </div>
            <form class="flex md:flex-row flex-col">
                <div class="md:w-6/12 flex flex-col justify-center items-center">
                    <!-- <img src="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" class="profile-image mt-8 mb-4"/> -->
                    <fieldset id="user_profile_input_fieldset">
                        <legend>Files</legend>
                        <!-- a list of already uploaded files -->
                        <ul>
                            <li>
                                <label>
                                    <input value="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" checked type="checkbox"/>
                                    {{ $user->profile_img }}
                                </label>
                            </li>
                        </ul>
                        <!-- our filepond input -->
                        <input type="file" name="input_profile" id="user_profile_input" style="display: none;"/>
                    </fieldset>
                </div>
                <div class="md:w-6/12 md:items-start items-center flex flex-col mt-8" class="w-6/12 flex flex-col mt-8">
                    <div class="form-group flex flex-row mb-3 ml-3">
                        <label class="switch">
                            <input type="checkbox" name="sounds" id="sounds" value="1" @if( $user_settings[2]->value == 1 ) checked @endif >
                            <span class="slider round"></span>
                        </label>
                        <span class="label">Dźwięki są wyłączone</span>
                    </div>
                    <div class="form-group flex flex-row mb-3 ml-3">
                        <label class="switch">
                            <input type="checkbox" name="notifications" id="notifications" value="1" @if( $user_settings[0]->value == 1 ) checked @endif >
                            <span class="slider round"></span>
                        </label>
                        <span class="label">Powiadomienia są wyłączone</span>
                    </div>
                    <div class="form-group flex flex-row mb-3 ml-3">
                        <label class="switch">
                            <input type="checkbox" name="press_on_enter" id="press_on_enter" value="1" @if( $user_settings[1]->value == 1 ) checked @endif >
                            <span class="slider round"></span>
                        </label>
                        <span class="label">Wyślij na [Enter] jest wyłączone</span>
                    </div>
                </div>
            </form>
            <div class="w-full h-full flex flex-row md:justify-end justify-center items-end">
                <button class="cta-btn btn-modal form-submit box-content rounded-xl" id="save_settings">Zapisz <i class="far fa-save"></i></button>
            </div>
        </div>

        <div id="friendsModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
            <div class="modal-title w-full text-center relative">Znajomi
                <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
            </div>
            <div class="w-full text-center mt-6">Wpisz nick i dodaj znajomego</div>
            <form class="flex flex-row items-center justify-around mt-2" id="add_friend_form" method="POST">
                @csrf
                <div class="input-group mr-4 relative">
                    <input class="form-input" type="text" name="nickname" id="nickname" required/>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Nickname</label>
                </div>
                <button class="cta-btn btn-modal form-submit box-content rounded-xl" id="add_friend" type="button">Dodaj <i class="fas fa-paper-plane"></i></button>
            </form>
            <div class="list flex flex-col md:flex-row md:flex-wrap flex-around overflow-y-auto overflow-x-hidden h-full">
                @foreach ($friends_data as $friend_id => $friend)
                    <div class="friend relative flex flex-row flex-wrap">
                        <div class="profile_container relative flex flex-row justify-center align-center items-center">
                            <img src="{{ asset('storage/profiles_miniatures/'.$friend['profile_img']) }}" class="profile-image"/>
                            @if ($friend['status'] == 0)
                                <i class="fas fa-user-clock waiting_friend"></i>
                            @endif
                        </div>
                        <div class="friend_name ml-2">{{ $friend['nick'] }}</div>
                        <i class="friend_name fas fa-ellipsis-v open_fast_menu ml-4" data="{{ $friend_id }}"></i>
                        <div class="fast_menu absolute flex flex-col z-10" style="display: none;">
                            @if ($friend['status'] == 0 && $friend['invite'] == 1)
                                <!-- Accept/Deceline menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Zaproszenie</div>
                                <button class="fast_menu_btn friendship_menu mb-2 w-full" id="acceptInvite" data="{{ $friend_id }}"><i class="fas fa-user-check"></i> Akceptuj</button>
                                <button class="fast_menu_btn friendship_menu mb-2 w-full" id="decelineInvite" data="{{ $friend_id }}"><i class="fas fa-user-minus"></i> Odrzuć</button>
                                <button class="fast_menu_btn friendship_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @elseif ($friend['status'] == 0 && $friend['invite'] == 0)
                                <!-- Cancel invite menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Zaproszenie</div>
                                <button class="fast_menu_btn friendship_menu mb-2 w-full" id="cancelInvite" data="{{ $friend_id }}"><i class="fas fa-user-slash"></i> Anuluj</button>
                                <button class="fast_menu_btn friendship_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @elseif ($friend['status'] == 1)
                                <!-- Friendship menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="fas fa-user-friends"></i> Znajomość</div>
                                <button class="fast_menu_btn friendship_menu mb-2 w-full" id="blockFriendship" data="{{ $friend_id }}"><i class="fas fa-comment-slash"></i> Zablokuj</button>
                                <button class="fast_menu_btn friendship_menu mb-2 w-full" id="deleteFriendship" data="{{ $friend_id }}"><i class="fas fa-user-slash"></i> Usuń</button>
                                <button class="fast_menu_btn friendship_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="roomsModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
            <div class="modal-title w-full text-center relative">Pokoje
                <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
            </div>

            <div class="w-full flex flex-row justify-evenly items-center mt-6">
                <div class="w-1/2 md:w-1/3 flex flex-col">
                    <span class="w-full text-center">Wyszukaj pokój</span>
                </div>
                <div class="hidden md:block md:w-1/3 text-center">
                    lub
                </div>
                <button class="w-1/2 md:w-1/3 flex-grow md:flex-grow-0 cta-btn btn-modal box-content rounded-xl modalToggle" data="addRoomModal" type="button">Utwórz pokój <i class="fas fa-users"></i></button>
            </div>
            <div class="input-group relative">
                <input class="form-input" type="text" name="search_room" id="search_room" required/>
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Nazwa pokoju</label>
            </div>
            <div class="list flex flex-col md:flex-row md:flex-wrap flex-around overflow-y-auto overflow-x-hidden h-full">
                @foreach ($rooms_data as $room_id => $room)
                    <div class="friend relative flex flex-row flex-wrap">
                        <div class="profile_container relative flex flex-row justify-center align-center items-center">
                            <img src="{{ asset('storage/room_miniatures/'.$room->room_img) }}" class="profile-image"/>
                            @if ($room->status == 0)
                                <i class="fas fa-user-clock waiting_friend"></i>
                            @endif
                        </div>
                        <div class="friend_name ml-2">{{ $room->room_name }}</div>
                        <i class="friend_name fas fa-ellipsis-v open_fast_menu ml-4" data="{{ $room_id }}"></i>
                        <div class="fast_menu absolute flex flex-col z-10" style="display: none;">
                            @if ($room->status == 0 && $room->admin_id != $user->id)
                                <!-- Accept/Deceline menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Zaproszenie</div>
                                <button class="fast_menu_btn room_menu mb-2 w-full" id="acceptInvite" data="{{ $room_id }}"><i class="fas fa-door-open"></i> Akceptuj</button>
                                <button class="fast_menu_btn room_menu mb-2 w-full" id="decelineInvite" data="{{ $room_id }}"><i class="fas fa-door-closed"></i> Odrzuć</button>
                                <button class="fast_menu_btn room_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @elseif ($room->status == 1 && $room->admin_id == $user->id)
                                <!-- Room owner menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Pokój</div>
                                <button class="fast_menu_btn room_menu mb-2 w-full deleteRoom" id="deleteRoom" data="{{ $room_id }}"><i class="far fa-trash-alt"></i> Usuń</button>
                                <button class="fast_menu_btn room_menu mb-2 w-full" id="settingsRoom" data="{{ $room_id }}"><i class="fas fa-cogs"></i> Ustawienia</button>
                                <button class="fast_menu_btn room_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @elseif ($room->status == 1)
                                <!-- Room menu -->
                                <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="fas fa-users-friends"></i> Pokój</div>
                                <button class="fast_menu_btn room_menu mb-2 w-full" id="blockRoom" data="{{ $room_id }}"><i class="fas fa-comment-slash"></i> Zablokuj</button>
                                <button class="fast_menu_btn room_menu mb-2 w-full" id="outRoom" data="{{ $room_id }}"><i class="fas fa-sign-out-alt"></i> Wyjdź</button>
                                <button class="fast_menu_btn room_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if( $room_id != 0 && $rooms_data[$room_id]->admin_id == $user->id )
            <div id="roomSettingsModal" class="modal-xl modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
                <div class="modal-title w-full mb-4 text-center relative">Ustawienia pokoju
                    <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
                </div>

                <div class="flex md:flex-row flex-col md:h-full h-4/5 overflow-y-hidden w-full">
                    <!-- Left column -->
                    <div class="flex flex-col md:border-r-2 md:pr-2 md:mr-2 md:h-5/6 flex-wrap" style="border-color: #4d5499;"> 
                        <input type="file" name="room_profile" class="file_input rounded-full mb-4" data-max-files="1" accept="image/png, image/jpeg, image/webp"/>
                        <div class="input-group relative">
                            <input class="form-input" type="text" name="update_room_name" id="update_room_name" value="{{ $rooms_data[$room_id]->room_name }}" required/>
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label>Nazwa pokoju</label>
                        </div>
                    </div>
                    <!-- Right column -->
                    <div class="w-full flex flex-col h-7/10 overflow-y-hidden"> 
                        <div class="w-full text-center">Wyrzuć znajomych</div>
                        <div class="list flex flex-col overflow-y-auto h-full pr-2 overflow-x-hidden">
                            @foreach ($roommates_data as $roommate_id => $roommate)
                                @if ($roommate['status'] == 1 && $roommate_id != $user->id)
                                    <div class="friend relative w-full flex flex-row flex-wrap border-b-2">
                                        <div class="profile_container relative flex flex-row justify-center align-center items-center">
                                            <img src="{{ asset('storage/profiles_miniatures/'.$roommate['profile_img']) }}" class="profile-image"/>
                                        </div>
                                        <div class="friend_name ml-2">{{ $roommate['nick'] }}</div>
                                        <div class="box_switch absolute inset-y-2/4  right-2">
                                            <label class="switch">
                                                <input type="checkbox" name="roommate[]" class="roommate" value="{{ $roommate_id }}" >
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:h-16 h-1/5 justify-between w-full md:items-end items-center">
                    <button type="button" class="deleteRoom settings-btn btn-danger btn-modal" data="{{ $room_id }}">Usuń pokój <i class="far fa-trash-alt"></i></button>
                    <button type="button" class="add-friends settings-btn btn-modal modalToggle" data="inviteFriendsModal">Zaproś znajomych <i class="fas fa-user-plus"></i></button>
                    <button type="button" class="cta-btn btn-modal form-submit box-content rounded-xl" id="update_room">Zapisz <i class="far fa-save"></i></button>
                </div>
            </div>

            <div id="inviteFriendsModal" class="modal-xl modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
                <div class="modal-title w-full text-center relative">Zaproś znajomych do pokoju
                    <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
                </div>

                <div class="flex md:flex-row md:flex-wrap flex-col overflow-x-hidden overflow-y-auto w-full mb-9">
                    @foreach ($friends_data as $friend_id => $friend)
                        @if ($friend['status'] == 1)
                            <div class="friend relative w-full flex flex-row flex-wrap border-b-2">
                                <div class="profile_container relative flex flex-row justify-center align-center items-center">
                                    <img src="{{ asset('storage/profiles_miniatures/'.$friend['profile_img']) }}" class="profile-image"/>
                                </div>
                                <div class="friend_name ml-2">{{ $friend['nick'] }}</div>
                                <div class="box_switch_modal absolute inset-y-2/4  right-2">
                                    <button type="button" class="btn_invite btn-modal text-center">Zaproś <i class="far fa-envelope"></i></button>
                                    <input type="checkbox" name="add_friend[]" class="add_friend_checkbox" value="{{ $friend_id }}" style="display: none;">
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <button type="button" class="back settings-btn btn-modal btn-danger absolute bottom-2 left-2 modalToggle" data="roomSettingsModal">Wróć <i class="fas fa-chevron-left"></i></button>
                <button type="button" class="cta-btn absolute bottom-2 right-2 form-submit box-content rounded-xl" id="send_invites">Zaproś <i class="fas fa-user-plus"></i></button>
            </div>
        @endif

        <div id="addRoomModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
            <div class="modal-title w-full text-center relative">Tworzenie pokoju
                <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
            </div>

            <div class="w-full flex flex-col mt-6">
                <span class="w-full text-sm text-center">Podaj nazwę pokoju lub pozostaw to pole puste ( {{ $user->nick }}_room)</span>
                <div class="input-group relative">
                    <input class="form-input" type="text" name="room_name" id="room_name" required/>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Nazwa pokoju</label>
                </div>
            </div>

            <div class="w-3/4 flex flex-col mt-6 mx-auto">
                <span class="w-full text-sm text-center">Wpisz nick i zaproś znajomego do pokoju</span>
                <div class="w-full flex flex-row justify-evenly mt-4">
                    <div class="input-group relative">
                        <input class="form-input" type="text" name="search_user" id="search_user" required/>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>Nickname</label>
                    </div>
                </div>
            </div>

            <div class="list flex flex-col overflow-y-auto pr-2 overflow-x-hidden">
                @foreach ($friends_data as $friend_id => $friend)
                    @if ($friend['status'] == 1)
                        <div class="friend relative w-full flex flex-row flex-wrap border-b-2">
                            <div class="profile_container relative flex flex-row justify-center align-center items-center">
                                <img src="{{ asset('storage/profiles_miniatures/'.$friend['profile_img']) }}" class="profile-image"/>
                            </div>
                            <div class="friend_name ml-2">{{ $friend['nick'] }}</div>
                            <div class="box_switch_modal absolute inset-y-2/4  right-2">
                                <button type="button" class="btn-modal btn_invite text-center">Zaproś <i class="far fa-envelope"></i></button>
                                <input type="checkbox" name="add_friend[]" class="add_friend_checkbox" value="{{ $friend_id }}" style="display: none;">
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <button class="cta-btn absolute bottom-2 right-2 form-submit box-content rounded-xl" id="save_room">Zapisz <i class="far fa-save"></i></button>
        </div>

        <div id="feedback_wrapper" class="absolute bottom-2 left-1/2 py-2 px-4 rounded-xl" style="display: none;"></div>
        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}"/>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <audio style="display:none;" id="notifySound">
            <source src="{{ asset('storage/sounds/mmm-2-tone-sexy.mp3') }}" type="audio/mpeg">
        </audio> 
        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
        <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
        <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
