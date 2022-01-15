<div class="w-full flex flex-row flex-wrap">
    <h2 class="heading w-full text-center md:text-left ">Twoje pokoje: </h2>
    <div class="list flex flex-col md:flex-row w-full flex-wrap">
        @foreach ($rooms_data as $room_id => $room)
            <a class="friend relative flex flex-row flex-wrap" href="/room/{{ $room_id }}">
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
                <div class="flex flex-col items-center justify-evenly">
                    <div class="room_name ml-2">{{ $room->room_name }}</div>
                    @if($room->status == 1)
                        <div class="room_last_msg ml-2">{{ $room->last_msg_user }}: {{ $room->last_msg_content }}</div>
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