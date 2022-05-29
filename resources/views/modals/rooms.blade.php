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