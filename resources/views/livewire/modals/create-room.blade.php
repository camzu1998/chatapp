<form >
    <div class="w-full flex flex-col mt-6">
        <span class="w-full text-sm text-center">Podaj nazwę pokoju lub pozostaw to pole puste ( {{ Auth::user()->nick }}_room)</span>
        <div class="input-group relative">
            <input class="form-input" type="text" wire:model.defer="room_name" required/>
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Nazwa pokoju</label>
        </div>
    </div>

    {{-- Todo: This will be added soon --}}
    {{--<div class="w-3/4 flex flex-col mt-6 mx-auto">--}}
    {{--    <span class="w-full text-sm text-center">Wpisz nick i zaproś znajomego do pokoju</span>--}}
    {{--    <div class="w-full flex flex-row justify-evenly mt-4">--}}
    {{--        <div class="input-group relative">--}}
    {{--            <input class="form-input" type="text" wire:model.debounce.500ms="search_user"/>--}}
    {{--            <span class="highlight"></span>--}}
    {{--            <span class="bar"></span>--}}
    {{--            <label>Nickname</label>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--</div>--}}

    <div class="list flex flex-col overflow-y-auto pr-2 overflow-x-hidden">
        @foreach (Auth::user()->friends() as $friend)
            @if ($friend->pivot->status == 1)
                <div class="friend relative w-full flex flex-row flex-wrap border-b-2">
                    <div class="profile_container relative flex flex-row justify-center align-center items-center">
                        <img src="{{ asset('storage/profiles_miniatures/'.$friend->profile_img) }}" class="profile-image"/>
                    </div>
                    <div class="friend_name ml-2">{{ $friend->nick }}</div>
                    <div class="box_switch_modal absolute inset-y-2/4  right-2">
                        <label for="checkbox_{{ $friend->nick }}" class="btn-modal btn_invite text-center">
                            <span>Zaproś <i class="far fa-envelope"></i></span>
                        </label>
                        <input type="checkbox" class="add_friend_checkbox" value="{{ $friend->id }}" id="checkbox_{{ $friend->nick }}" wire:model.defer="add_friend.{{ $friend->id }}" style="display: none;">
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    @if (session()->has('success'))
        <div class="bg-green-500 hover:bg-green-600 w-7/12 text-center rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    <button type="button" class="cta-btn absolute bottom-2 right-2 form-submit box-content rounded-xl" wire:click="store" >Zapisz <i class="far fa-save"></i></button>
</form>