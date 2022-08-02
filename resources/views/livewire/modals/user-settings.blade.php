<form class="md:w-6/12 md:items-start items-center flex flex-col mt-8 w-6/12 flex flex-col mt-8">
    @foreach (Auth::user()->userSettings as $userSetting)
        <div class="form-group flex flex-row mb-3 ml-3">
            <label class="switch">
                <input type="checkbox" id="{{ $userSetting->name }}" wire:model.defer="userSettings.{{ $userSetting->name }}" value="1">
                <span class="slider round"></span>
            </label>
            <span class="label">{{ __($userSetting->name) }}</span>
        </div>
    @endforeach

        <div class="absolute inset-x-0 bottom-0">
            @if (session()->has('success'))
                <div class="bg-green-500 hover:bg-green-600 w-7/12 text-center rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            <button class="cta-btn btn-modal absolute right-2 bottom-1 form-submit box-content rounded-xl" wire:click="save" type="button">Zapisz <i class="far fa-save"></i></button>
        </div>
</form>