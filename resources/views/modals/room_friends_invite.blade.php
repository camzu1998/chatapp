<form id="inviteFriendsModal" class="modal-xl modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
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
</form>