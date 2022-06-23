require('./bootstrap');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$( document ).ready(function() {
    FilePond.registerPlugin(
        FilePondPluginImagePreview
    );
    $('.file_input').filepond({
        allowMultiple: false,
        stylePanelLayout: 'compact circle',
        imagePreviewHeight: 170,
        imageCropAspectRatio: '1:1',
        styleLoadIndicatorPosition: 'center bottom',
        styleProgressIndicatorPosition: 'right bottom',
        styleButtonRemoveItemPosition: 'left bottom',
        styleButtonProcessItemPosition: 'right bottom',
        server: {
            url: '/room/'+$('#room_id').val(),
            process: '/upload_profile',
            revert: {
                url: '/revert_profile?id='+window.id,
                method: 'POST',
                withCredentials: false,
                headers: {},
                timeout: 7000,
                onload: null,
                onerror: null,
                ondata: null
            },
            restore: '/get_profile?name=',
            load: '/get_profile?name=',
            fetch: '/',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        files: [
            {
                source: $('#room_profile_img').val(),
                options: {
                    type: 'local',
                },
            },
        ],
    });
    $('#user_profile_input').filepond({
        allowMultiple: false,
        stylePanelLayout: 'compact circle',
        imagePreviewHeight: 170,
        imageCropAspectRatio: '1:1',
        styleLoadIndicatorPosition: 'center bottom',
        styleProgressIndicatorPosition: 'right bottom',
        styleButtonRemoveItemPosition: 'left bottom',
        styleButtonProcessItemPosition: 'right bottom',
        server: {
            url: '/user/'+window.user.id,
            process: '/upload_profile',
            revert: {
                url: '/revert_profile?id='+window.id,
                method: 'POST',
                withCredentials: false,
                headers: {},
                timeout: 7000,
                onload: null,
                onerror: null,
                ondata: null
            },
            restore: '/get_profile?name=',
            load: '/get_profile?name=',
            fetch: '/',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        files: [
            {
                source: window.user.profile_img,
                options: {
                    type: 'local',
                },
            },
        ],
    });
    const chat_file = FilePond.create(document.getElementById('file'));
    chat_file.setOptions({
        server: {
            url: '/chat/file',
            process: {
                url: '/'+$('#room_id').val(),
                onload: (response) => {
                    chat_file.removeFile();
                },
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
    });
});

$('#toggle-menu').click(function(){
    $('#user-dashboard')
        .css("display", "flex")
        .hide()
        .fadeIn();
});
$('.open_fast_menu').click(function(e){
    e.preventDefault();
    $(this).siblings('.fast_menu').fadeIn();
});
$('.cancel_fast_menu').click(function(){
    $('.fast_menu').fadeOut();
});
$('#close-menu').click(function(){
    $('#user-dashboard').fadeOut();
});
$('.modalToggle').click(function(){
    $('.modal').fadeOut();
    $('.full-shadow').fadeIn();
    $('#'+$(this).attr('data')).fadeIn();
});
$('.close').click(function(){
    $('.full-shadow').fadeOut();
    $('.modal').fadeOut();
    $('.fast_menu').fadeOut();
});
$('.full-shadow').click(function(){
    $('.full-shadow').fadeOut();
    $('.modal').fadeOut();
    $('.fast_menu').fadeOut();
    $('#image-full-screen').fadeOut();
});
// ROOMS
$('#save_room').click(function(){
    var fd = new FormData();
    var friends = $('.add_friend_checkbox:checked');

    // Room name
    fd.append('room_name', $('#room_name').val());
    // Check friends selected or not
    if(friends.length > 0 )
        friends.each(function(){
            fd.append('add_friend[]', $(this).val());
        });

    $.ajax({
        method: 'post',
        url: '/room',
        data: fd,
        contentType: false,
        processData: false
    }).always(function(res){
        window.location.reload(true);
    });
});
$('#update_room').click(function(){
    $.ajax({
        method: 'put',
        url: '/room/'+$('#room_id').val()+'/update',
        data: $('#roomSettingsModal').serialize(),
    }).always(function(res){
        window.location.reload(true);
    });
});
$('#send_invites').click(function(){
    $.ajax({
        method: 'post',
        url: '/room/'+$('#room_id').val()+'/invite',
        data: $('#inviteFriendsModal').serialize(),
    }).always(function(res){
        window.location.reload(true);
    });
});
$('.deleteRoom').click(function(){
    $.ajax({
        type: 'DELETE',
        url: '/room/'+$('#room_id').val(),
    }).always(function(res){
        // window.location.reload(true);
    });
});
$('.room_menu').click(function(){
    if($(this).hasClass('cancel_fast_menu')){
        return false;
    }
    var room_id = $(this).attr('data');

    $.ajax({
        method: 'put',
        url: '/room/'+room_id,
        data: 'button='+$(this).attr('id'),
    }).always(function(res){
         window.location.reload(true);
    });
});
$('body').on('click', '.btn_invite', function(){
    $(this).siblings('.add_friend_checkbox').prop('checked', true);
    $(this).html('Anuluj <i class="fas fa-times"></i>');
    $(this).addClass('cancel_invite').removeClass('btn_invite');
});
$('body').on('click', '.cancel_invite', function(){
    $(this).siblings('.add_friend_checkbox').prop('checked', false);
    $(this).html('Zaproś <i class="far fa-envelope"></i>');
    $(this).addClass('btn_invite').removeClass('cancel_invite');
});
// FRIENDSHIP
$('#add_friend').click(function(){
    $.ajax({
        method: 'post',
        url: '/friendship',
        data: $('#add_friend_form').serialize(),
    }).always(function(res){
        window.location.reload(true);
    });
});
$('.friendship_menu').click(function(){
    if($(this).hasClass('cancel_fast_menu')){
        return false;
    }
    var friend_id = $(this).attr('data');

    $.ajax({
        method: 'put',
        url: '/friendship/'+friend_id,
        data: 'button='+$(this).attr('id'),
    }).always(function(res){
        window.location.reload(true);
    });
});

//CHAT
$('div').on('click', '.content-image', function(){
    var src = $(this).prop('src');
    $('#image-full-screen').prop('src', src);
    $('#image-full-screen').fadeIn();
    $('.full-shadow').fadeIn();

    return false;
});
$('#content').keypress(function(e){
    if(e.which === 13 && !e.shiftKey) {
        if($('#press_on_enter').prop('checked')){
            e.preventDefault();

            $('#send').trigger( "click" );
        }
    }
});
$('#send').on( "click", function() {
    var user_id = $('#user_id').val();
    var img_ext = ['png', 'jpg', 'webp', 'gif', 'svg', 'jpeg'];
    var fd = new FormData();

    fd.append('_token', $('#token').val());
    fd.append('content', $('#content').val());

    $.ajax({
        method: 'post',
        url: '/chat/message/'+$('#room_id').val(),
        data: fd,
        contentType: false,
        processData: false
    }).always(function(res){
        var html = '';
        for(var i = 0; i < res.messages.length; i++){
            var msg = res.messages[i];
            var user = res.msg_users;
            var content = '';
            if(msg.file_id != 0){
                var file = res.files[msg.file_id];
                if($.inArray( file.ext, img_ext ) != -1){
                    content = '<img src="/storage/'+file.path+'" alt="'+file.filename+'" class="content-image">';
                }else{
                    content = '<p class="msg-file"> <a href="/storage/'+file.path+'"> <i class="far fa-file"></i>'+file.filename+' </a> </p> ';
                }
            }else{
                content = msg.content;
            }
            if(user_id == msg.user_id){
                html += '<div class="msg msg-right mb-12 relative p-2">';
            }else{
                html += '<div class="msg msg-left mb-12 relative p-2">';
            }
            html += '<img src="/storage/profiles_miniatures/'+user[msg.user_id].profile_img+'" class="msg-image absolute"/><div class="msg-content"><span class="msg-user_name">'+user[msg.user_id].nick+'</span><p class="msg-content-p" >'+content+'</p><span class="msg-date"></span></div></div>';
        }
        $('#messagesList').html(html).animate(
            { scrollTop: 1000000}
        );
    });

    $('#content').val('');

    return false;
});
function load_messages(){
    var user_id = $('#user_id').val();
    var room_id = $('#room_id').val();
    var img_ext = ['png', 'jpg', 'webp', 'gif', 'svg', 'jpeg'];
    var msg_switch = false;

    $.ajax({
        method: 'GET',
        url:    '/get_msg/'+room_id,
        data:   {_token: $('#token').val()}
    }).always(function(res){
        var html = '';
        for(var i = 0; i < res.messages.length; i++){
            var msg = res.messages[i];
            var user = res.msg_users;
            var content = '';
            if(msg.file_id != 0){
                var file = res.files[msg.file_id];
                if($.inArray( file.ext, img_ext ) != -1){
                    content = '<img src="/storage/'+file.path+'" alt="'+file.filename+'" class="content-image">';
                }else{
                    content = '<p class="msg-file"> <a href="/storage/'+file.path+'"> <i class="far fa-file"></i>'+file.filename+' </a> </p> ';
                }
            }else{
                content = msg.content;
            }

            if(user_id == msg.user_id){
                html += '<div class="msg msg-right mb-12 relative p-2">';
            }else{
                html += '<div class="msg msg-left mb-12 relative p-2">';
            }
            html += '<img src="/storage/profiles_miniatures/'+user[msg.user_id].profile_img+'" class="msg-image absolute"/><div class="msg-content"><span class="msg-user_name">'+user[msg.user_id].nick+'</span><p class="msg-content-p" >'+content+'</p><span class="msg-date"></span></div></div>';
        }
        $('#messagesList').html(html).animate(
            { scrollTop: 1000000}
        );

        if($('#sounds').prop('checked'))
            $('#notifySound').get(0).play()
    });

    return false;
}
//Refresh room message

setTimeout(() => {
    if($('#room_id').val() !== undefined) {
        window.Echo.private('room.'+$('#room_id').val()).listen('.NewMessageEvent', event => {
            console.log(event);
            load_messages();
        });
    }
    window.Echo.private('room_member').listen('.NewMessageEvent', event => {
        console.log(event);
        load_messages();
    });


    window.Echo.channel(`channel-test`)
        .listen('.TestEvent', e => {
            console.log(e)
        })
}, 1000);

var notifyWorker = new Worker('/js/notification.js');

notifyWorker.onmessage = function(e) {
    var res = e.data[0];

    if(e.data.sum_unreaded != 0){
        $('#roomsModalBtn').children('.btn-notify').html(e.data.sum_unreaded).fadeIn();
    }else{
        $('#roomsModalBtn').children('.btn-notify').html("").fadeOut();
    }
    if(typeof res !== undefined && res !== undefined && res.room_id != 0){
        var panel_room = $('#panel_room_'+res.room_id);
        if(panel_room.length != 0){
            panel_room.children('.profile_container').children('.unreaded').html(res.unreaded);
            panel_room.children('.room_info').children('.room_last_msg').html(res.user+': '+res.content);
        }
        var notification = new Notification("Użytkownik "+res.user+" wysłał wiadomość do pokoju "+res.room); //Todo: move to NotificationController
    }
}

//Check all room
setInterval(function(){
    notifyWorker.postMessage({name: "check_messages", token: $('#token').val()});

    return false;
}, 30000);

if (Notification.permission === "default") {
    Notification.requestPermission(function (permission) {
        // If the user accepts, let's create a notification
        if (permission === "granted") {
            new Notification("Hi there :)"); //new Notification('To do list', { body: text, icon: img }); Todo:better notification
        }
    });
}