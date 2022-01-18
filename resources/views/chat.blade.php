<img id="image-full-screen" src="" style="display:none;"/>
<!-- Messages -->
<div id="messagesList" class="w-full flex flex-col-reverse text-gray-200 px-12 md:pt-8">
    @foreach ($messages as $msg)
        @if ($msg->user_id != $user->id)
            <div class="msg msg-left mb-12 relative p-2">
        @elseif ($msg->user_id == $user->id)
            <div class="msg msg-right mb-12 relative p-2">
        @endif
                <img src="{{ asset('storage/profiles_miniatures/'.$msg_users[$msg->user_id]['profile_img']) }}" class="msg-image absolute"/>
                <div class="msg-content">
                    <span class="msg-user_name">{{ $msg_users[$msg->user_id]['nick'] }}</span>
                    <p class="msg-content-p" >
                        @if ($msg->content != '')
                            {{ $msg->content }}
                        @elseif ($msg->file_id != 0 && !in_array($files[$msg->file_id][0]->ext, $img_ext) )
                            <a href="{{ asset('storage/'.$files[$msg->file_id][0]->path) }}"><i class="far fa-file"></i> {{ $files[$msg->file_id][0]->filename }} </a> 
                        @else
                            <img src="{{ asset('storage/'.$files[$msg->file_id][0]->path) }}" alt="{{ $files[$msg->file_id][0]->filename }}" class="content-image">
                        @endif
                    </p>
                    @if ($msg->user_id != $user->id)
                        <span class="msg-date absolute top-1 right-1">{{ $msg->created_at }}</span>
                    @elseif ($msg->user_id == $user->id)
                        <span class="msg-date absolute top-1 left-1">{{ $msg->created_at }}</span>
                    @endif
                </div>
            </div>
    @endforeach
</div>
<!-- Message Form -->
<div class="flex flex-col fixed bottom-2 right-0 md:right-2 formContainer">
    <form class="w-full h-full flex flex-col relative" id="msgForm" enctype='multipart/form-data'>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <input type="hidden" name="room_id" id="room_id" value="{{ $room_id }}">
        <input type="hidden" name="newest_id" id="newest_id" value="{{ $newest_msg }}">
        <input type="hidden" name="nick" id="nick" placeholder="Nick" value="{{ $user->nick }}"/>
        <textarea class="w-full" name="content" id="content" placeholder="Napisz wiadomość..."></textarea>
        <div class="w-full flex flex-row justify-between flex-grow msg-bar">
            <input type="file" name="file" id="file"/>
            <button class="msg-submit" id="send" type="button"><i class="far fa-paper-plane"></i></button>
        </div>
    </form>
</div>