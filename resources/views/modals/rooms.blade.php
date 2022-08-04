<div id="roomsModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
    <div class="modal-title w-full text-center relative">Pokoje
        <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
    </div>

    <div class="w-full flex flex-row justify-evenly items-center mt-6">
        <div class="w-1/2 md:w-1/3 flex flex-col">
            <span class="w-full text-center">Wyszukaj pokój</span>
        </div>
        <div class="hidden md:block md:w-1/3 text-center">
            lub
        </div>
        <button class="w-1/2 md:w-1/3 flex-grow md:flex-grow-0 cta-btn btn-modal box-content rounded-xl modalToggle" data="addRoomModal" type="button">Utwórz pokój <i class="fas fa-users"></i></button>
    </div>
    @livewire('modals.rooms')
</div>