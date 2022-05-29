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