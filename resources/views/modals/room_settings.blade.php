<form id="roomSettingsModal" class="modal-xl modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
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
</form>