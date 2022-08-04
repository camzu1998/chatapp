<form>
    <div class="input-group relative">
        <input class="form-input" type="text" wire:model.debounce.500ms="search" id="search_room" required/>
        <span class="highlight"></span>
        <span class="bar"></span>
        <label for="search_room">Nazwa pokoju</label>
    </div>
    <div class="list flex flex-col md:flex-row md:flex-wrap flex-around overflow-y-auto overflow-x-hidden h-full">
        @foreach ($roomsMember as $roomMember)
            <div class="friend relative flex flex-row flex-wrap">
                <div class="profile_container relative flex flex-row justify-center align-center items-center">
                    <img src="{{ asset('storage/room_miniatures/'.$roomMember->room->profile_img) }}" class="profile-image"/>
                    @if ($roomMember->status == 0)
                        <i class="fas fa-user-clock waiting_friend"></i>
                    @endif
                </div>
                <div class="friend_name ml-2">{{ $roomMember->room->room_name }}</div>
                <i class="friend_name fas fa-ellipsis-v open_fast_menu ml-4" data="{{ $roomMember->room->id }}"></i>
                <div class="fast_menu absolute flex flex-col z-10" style="display: none;">
                    @if ($roomMember->status == 0 && $roomMember->room->admin_id != auth()->user()->id)
                        <!-- Accept/Deceline menu -->
                        <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Zaproszenie</div>
                        <button class="fast_menu_btn room_menu mb-2 w-full" id="acceptInvite" data="{{ $roomMember->room->id }}"><i class="fas fa-door-open"></i> Akceptuj</button>
                        <button class="fast_menu_btn room_menu mb-2 w-full" id="decelineInvite" data="{{ $roomMember->room->id }}"><i class="fas fa-door-closed"></i> Odrzuć</button>
                        <button class="fast_menu_btn room_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                    @elseif ($roomMember->status == 1 && $roomMember->room->admin_id == auth()->user()->id)
                        <!-- Room owner menu -->
                        <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="far fa-envelope"></i> Pokój</div>
                        <button class="fast_menu_btn room_menu mb-2 w-full deleteRoom" id="deleteRoom" data="{{ $roomMember->room->id }}"><i class="far fa-trash-alt"></i> Usuń</button>
                        <button class="fast_menu_btn room_menu mb-2 w-full" id="settingsRoom" data="{{ $roomMember->room->id }}"><i class="fas fa-cogs"></i> Ustawienia</button>
                        <button class="fast_menu_btn room_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                    @elseif ($roomMember->status == 1)
                        <!-- Room menu -->
                        <div class="heading mt-4 border-b pb-2 mb-2 text-center"><i class="fas fa-users-friends"></i> Pokój</div>
                        <button class="fast_menu_btn room_menu mb-2 w-full" id="blockRoom" data="{{ $roomMember->room->id }}"><i class="fas fa-comment-slash"></i> Zablokuj</button>
                        <button class="fast_menu_btn room_menu mb-2 w-full" id="outRoom" data="{{ $roomMember->room->id }}"><i class="fas fa-sign-out-alt"></i> Wyjdź</button>
                        <button class="fast_menu_btn room_menu mb-4 w-full cancel_fast_menu"><i class="fas fa-times"></i> Zamknij menu</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</form>