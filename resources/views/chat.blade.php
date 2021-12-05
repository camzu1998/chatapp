<!-- Messages -->
<div id="messagesList" class="w-full flex flex-col-reverse text-gray-200 px-12 pt-8" style="height: calc(100vh - 100px); overflow-y: auto;">
    @foreach ($messages as $msg)
        @if ($msg->user_id != $user->id)
            <div class="msg msg-left mb-12 relative p-2">
        @elseif ($msg->user_id == $user->id)
            <div class="msg msg-right mb-12 relative p-2">
        @endif
                <img src="{{ asset('storage/profiles_miniatures/'.$msg_users[$msg->user_id]['profile_img']) }}" class="msg-image absolute"/>
                <div class="msg-content">
                    <span class="msg-user_name">{{ $msg_users[$msg->user_id]['nick'] }}</span>
                    <p class="msg-content-p" >{{ $msg->content }}</p>
                    <span class="msg-date"></span>
                        @if ($msg->file_id != 0)
                            <p class="msg-file"> <a href="{{ asset('storage/'.$files[$msg->file_id][0]->path) }}"><i class="far fa-file"></i> {{ $files[$msg->file_id][0]->filename }} </a> </p> 
                        @endif
                </div>
            </div>
    @endforeach
</div>
<!-- Message Form -->
<div class="flex flex-row fixed bottom-0 right-0 formContainer">
    <form class="w-full flex flex-row" id="msgForm" enctype='multipart/form-data'>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <input type="hidden" name="room_id" id="room_id" value="{{ $room_id }}">
        <input type="hidden" name="newest_id" id="newest_id" value="{{ $newest_msg }}">
        <input type="hidden" name="nick" id="nick" placeholder="Nick" value="{{ $user->nick }}"/>
        <textarea class="w-10/12" name="content" id="content" placeholder="Napisz wiadomość..."></textarea>
        <div class="flex flex-col flex-grow">
            <button class="cta-btn msg-submit" id="send">Wyślij <i class="far fa-save"></i></button>
            <label for="file" class="form-input file-input text-center">Wgraj plik <i class="fas fa-upload"></i></label>
            <input type="file" name="file" id="file" style="display: none;"/>
        </div>
    </form>
</div>