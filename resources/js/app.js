require('./bootstrap');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
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
        process: '/upload',
        revert: {
            url: '/revert?id='+window.id,
            method: 'POST',
            withCredentials: false,
            headers: {},
            timeout: 7000,
            onload: null,
            onerror: null,
            ondata: null
        },
        restore: '/get_image?name=',
        load: '/get_image?name=',
        fetch: '/',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    },
});
const chat_file = FilePond.create(document.getElementById('file'));
chat_file.setOptions({
    server: {
        url: '/chat/file/'+$('#room_id').val(),
        process: {
            url: '/',
            onload: (response) => {
                chat_file.removeFile();
            },
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    },
});
$('#toggle-menu').click(function(){
    $('#user-dashboard')
    .css("display", "flex")
    .hide()
    .fadeIn();
});
$('.open_fast_menu').click(function(){
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
$('#save_settings').click(function(){
    var fd = new FormData();
    var files = $('#input_profile')[0].files;
    
    // Check file selected or not
    if(files.length > 0 )
        fd.append('input_profile',files[0]);

    var sounds = 0;
    if($('#sounds').prop('checked'))
        sounds = 1;
    var notifications = 0;
    if($('#notifications').prop('checked'))
        notifications = 1;
    var press_on_enter = 0;
    if($('#press_on_enter').prop('checked'))
        press_on_enter = 1;

    fd.append('_token', $('#token').val());
    fd.append('sounds', sounds);
    fd.append('notifications', notifications);
    fd.append('press_on_enter', press_on_enter);

    $.ajax({
        method: 'post',
        url: '/save_settings',
        data: fd,
        contentType: false,
        processData: false
    }).always(function(res){
        window.location.reload(true);
    });
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
$('.content-image').click(function(){
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
                var file = res.files[msg.file_id][0];
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
        $('#messagesList').html(html);
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
                var file = res.files[msg.file_id][0];
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
        $('#messagesList').html(html);

        if($('#sounds').prop('checked'))
            $('#notifySound').get(0).play()
    });

    return false;
}
//Refresh room message
var worker = new Worker('/js/worker.js');
var notifyWorker = new Worker('/js/notification.js');

worker.onmessage = function(e) {
    var newest_id = e.data;
    if(newest_id > $('#newest_id').val()){
        notifyWorker.postMessage({name: "notify_room_message", token: $('#token').val(), room: $('#room_id').val()});
        $('#newest_id').val(newest_id);
        load_messages();
    }
    console.log('Data received from worker');
}

worker.addEventListener('error', function(e) {
    alert('wystapil blad w linii: ' + e.lineno +
          ' w pliku: ' + e.filename + '.' +
          'Tresc bledu: ' + e.message);
}, false);

setInterval(function(){
    if($('#room_id').val() !== undefined){
        worker.postMessage([$('#token').val(), $('#room_id').val()]);
    }

    return false;
}, 3000);
//Check all room
setInterval(function(){
    notifyWorker.postMessage({name: "check_messages", token: $('#token').val()});

    return false;
}, 30000);

Notification.requestPermission(function (permission) {
    // If the user accepts, let's create a notification
    if (permission === "granted") {
        notifyWorker.postMessage({name: "notification"});
    }
});