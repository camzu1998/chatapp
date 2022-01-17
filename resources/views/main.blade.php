<div class="w-full flex flex-row flex-wrap">
    <h2 class="heading w-full text-center md:text-left ">Twoje pokoje: </h2>
    <div class="list flex flex-col md:flex-row w-full flex-wrap">
        @foreach ($rooms_data as $room_id => $room)
            <a class="friend room relative flex flex-row flex-wrap" href="/room/{{ $room_id }}" id="panel_room_{{ $room_id }}">
                <div class="profile_container relative flex flex-row justify-center align-center items-center">
                    <img src="{{ asset('storage/room_miniatures/'.$room->room_img) }}" class="profile-image"/>
                    @if ($room->status == 0)
                        <i class="fas fa-user-clock waiting_friend"></i>
                    @endif
                    @if($room->unreaded != 0 && $room->status == 1)
                        <span class="unreaded absolute -top-1 -left-1">{{ $room->unreaded }}</span>
                    @endif
                    @if($room->status == 1)
                        <img src="{{ asset('storage/profiles_miniatures/'.$room->last_msg_user_img) }}" class="profile-image-room absolute bottom-2 -right-1"/>
                    @endif
                </div>
                <div class="flex flex-col items-center justify-evenly room_info">
                    <div class="room_name ml-2">{{ $room->room_name }}</div>
                    @if($room->status == 1)
                        <div class="room_last_msg ml-2">{{ $room->last_msg_user }}: {{ $room->last_msg_content }}</div>
                    @endif
                </div>
                <i class="friend_name fas fa-ellipsis-v open_fast_menu ml-4 text-center" data="{{ $room_id }}"></i>
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
            </a>
        @endforeach
    </div>
</div>
<div class="w-full flex flex-row flex-wrap">
    <h2 class="heading w-full text-center md:text-left">Twoi znajomi: </h2>
    <div class="list flex flex-col md:flex-row w-full flex-wrap">
        @foreach ($friends_data as $friend_id => $friend)
            @if($friend['status'] == 1)
                <div class="friend flex flex-row">
                    <div class="profile_container relative flex flex-row justify-center align-center items-center">
                        <img src="{{ asset('storage/profiles_miniatures/'.$friend['profile_img']) }}" class="profile-image"/>
                    </div>
                    <div class="friend_name ml-2">{{ $friend['nick'] }}</div>
                </div>
            @endif
        @endforeach
    </div>
</div>