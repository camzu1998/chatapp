<div class="w-full flex flex-col">
    <h2 class="heading">Twoje pokoje: </h2>
    <div class="list"></div>
</div>
<div class="w-full flex flex-col">
    <h2 class="heading">Twoi znajomi: </h2>
    <div class="list">
        @foreach ($friends_data as $friend_id => $friend)
            @if($friend['status'] == 1)
                <div class="friend flex flex-row">
                    <div class="profile_container">
                        <img src="{{ asset('storage/profiles_miniatures/'.$friend['profile_img']) }}" class="profile-image"/>
                    </div>
                    <div class="friend_name ml-2">{{ $friend['nick'] }}</div>
                </div>
            @endif
        @endforeach
    </div>
</div>