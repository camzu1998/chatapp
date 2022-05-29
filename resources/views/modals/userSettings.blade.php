<div id="settingsModal" class="modal flex flex-col absolute left-2/4 top-2/4 p-4 rounded-xl" style="display:none">
    <div class="modal-title w-full text-center relative">Ustawienia
        <span class="close absolute top-0 left-full"><i class="fas fa-times"></i></span>
    </div>
    <form class="flex md:flex-row flex-col">
        <div class="md:w-6/12 flex flex-col justify-center items-center">
            <!-- <img src="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" class="profile-image mt-8 mb-4"/> -->
            <fieldset id="user_profile_input_fieldset">
                <legend>Files</legend>
                <!-- a list of already uploaded files -->
                <ul>
                    <li>
                        <label>
                            <input value="{{ asset('storage/profiles_miniatures/'.$user->profile_img) }}" checked type="checkbox"/>
                            {{ $user->profile_img }}
                        </label>
                    </li>
                </ul>
                <!-- our filepond input -->
                <input type="file" name="input_profile" id="user_profile_input" style="display: none;"/>
            </fieldset>
        </div>
        <div class="md:w-6/12 md:items-start items-center flex flex-col mt-8" class="w-6/12 flex flex-col mt-8">
            <div class="form-group flex flex-row mb-3 ml-3">
                <label class="switch">
                    <input type="checkbox" name="sounds" id="sounds" value="1" @if( $user_settings[2]->value == 1 ) checked @endif >
                    <span class="slider round"></span>
                </label>
                <span class="label">Dźwięki są wyłączone</span>
            </div>
            <div class="form-group flex flex-row mb-3 ml-3">
                <label class="switch">
                    <input type="checkbox" name="notifications" id="notifications" value="1" @if( $user_settings[0]->value == 1 ) checked @endif >
                    <span class="slider round"></span>
                </label>
                <span class="label">Powiadomienia są wyłączone</span>
            </div>
            <div class="form-group flex flex-row mb-3 ml-3">
                <label class="switch">
                    <input type="checkbox" name="press_on_enter" id="press_on_enter" value="1" @if( $user_settings[1]->value == 1 ) checked @endif >
                    <span class="slider round"></span>
                </label>
                <span class="label">Wyślij na [Enter] jest wyłączone</span>
            </div>
        </div>
    </form>
    <div class="w-full h-full flex flex-row md:justify-end justify-center items-end">
        <button class="cta-btn btn-modal form-submit box-content rounded-xl" id="save_settings">Zapisz <i class="far fa-save"></i></button>
    </div>
</div>