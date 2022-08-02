<div id="settingsModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
    <div class="modal-title w-full text-center relative">Ustawienia
        <span class="close absolute top-0 left-full text-xl"><i class="fas fa-times"></i></span>
    </div>
    <div class="flex md:flex-row flex-col">
        <div class="md:w-6/12 flex flex-col justify-center items-center">
            <input type="file" name="input_profile" id="user_profile_input" class="rounded-full mb-4" data-max-files="1" accept="image/png, image/jpeg, image/webp"/>
        </div>
        @livewire('modals.user-settings')
    </div>
</div>