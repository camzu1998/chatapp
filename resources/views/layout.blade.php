<!DOCTYPE html>
<html lang="pl">
    <head>
        @include('partials.head')
        @yield('head')
        @livewireStyles
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
                    <img src="{{ asset('storage/profiles_miniatures/'.Auth::user()->profile_img) }}" class="profile-image md:block hidden mt-8 mb-4"/>
                    <div class="hello-user  md:w-full md:text-center md:mb-8">
                    Witaj {{ Auth::user()->nick }}
                        @if( !empty($room) && $room->id != 0 )
                            w pokoju {{ $room->room_name }}
                        @endif
                    </div>
                    <!-- Btns -->
                    <div class="btns-box flex-grow flex flex-col w-full"> 
                        @if( !empty($room) && $room->id != 0 && $room->admin_id == Auth::id() )
                            <button class="btn animated-button my-1 md:my-8 modalToggle btn-secondary" data="roomSettingsModal"><span class="relative w-full text-center z-10"><i class="fas fa-sliders-h"></i> Ustawienia pokoju</span></button>
                        @endif
                        <button class="btn animated-button victoria-three my-1 md:my-4 w-full modalToggle" data="friendsModal"><span class="relative w-full text-center z-10"><i class="fas fa-users"></i> Znajomi</span></button>
                        <button class="btn animated-button victoria-three my-1 md:my-4 w-full modalToggle" data="roomsModal" id="roomsModalBtn">
                            <span class="relative w-full text-center z-10"><i class="far fa-comment"></i> Pokoje</span>
                            <span class="absolute right-2 top-1/2 text-center z-10 btn-notify" style="display:none;"></span>
                        </button>
                        <button class="btn animated-button victoria-three my-1 md:my-4 w-full modalToggle" data="settingsModal"><span class="relative w-full text-center z-10"><i class="fas fa-cogs"></i> Ustawienia</span></button>
                        @if( !empty($room) && $room->id != 0 )
                            <a href="/" class="btn animated-button btn-gray my-1 md:my-8 w-full"><span class="relative w-full text-center z-10"><i class="fas fa-arrow-left"></i> Wróc na stronę główną</span></a>
                        @endif
                        <a href="/logout" class="btn animated-button my-1 md:mt-4 w-full logout"><span class="relative w-full text-center z-10"><i class="fas fa-sign-out-alt"></i> Wyloguj się</span></a>
                    </div>
                </div>
            </div>

            <div class="content-panel flex flex-col flex-grow h-screen">
                @yield('content')
            </div>

        </div>
        <div class="full-shadow w-screen h-screen absolute top-0 left-0" style="display:none;"></div>
{{--        {{ dd(Auth::user()->friends()) }}--}}
        @include('modals.user-settings')
{{--        @include('modals.friends')--}}
        @include('modals.rooms')

        @include('modals.create-room')

        @if( !empty($room) && $room->id != 0 && $room->admin_id == Auth::id() )
{{--            @include('modals.room_settings')--}}
{{--            @include('modals.room_friends_invite')--}}
            <script>
                var room_id = {{ $room->id }};
            </script>
        @endif

        <div id="feedback_wrapper" class="absolute bottom-2 left-1/2 py-2 px-4 rounded-xl" style="display: none;"></div>
        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::id() }}"/>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <audio style="display:none;" id="notifySound">
            <source src="{{ asset('storage/sounds/mmm-2-tone-sexy.mp3') }}" type="audio/mpeg">
        </audio> 
        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
        <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
        <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
        <script>
            var user = {!! auth()->user()->toJson() !!}
        </script>
        <script src="{{ mix('/js/app.js') }}"></script>

        <script src="//chatapp.loc:6001/socket.io/socket.io.js"></script>
        @livewireScripts
        @yield('scripts')
    </body>
</html>
