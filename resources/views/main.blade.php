<div class="w-full flex flex-col">
    <h2 class="heading">Twoje pokoje: </h2>
    <div class="list"></div>
</div>
<div class="w-full flex flex-col">
    <h2 class="heading">Twoi znajomi: </h2>
    <div class="list">
        @foreach ($friends as $friend)
            @if($friends_data[$friend['id']]['status'] == 1)
                <div class="friend flex flex-row">
                    <div class="profile_container">
                        <img src="{{ asset('storage/profiles_miniatures/'.$friends_data[$friend['id']]['profile_img']) }}" class="profile-image"/>
                    </div>
                    <div class="friend_name ml-2">{{ $friends_data[$friend['id']]['nick'] }}</div>
                </div>
            @endif
        @endforeach
    </div>
</div>