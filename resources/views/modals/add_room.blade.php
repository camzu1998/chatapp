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