require('./bootstrap');
$('#openSettings').click(function(){
    $('.full-shadow').fadeIn();
    $('#settingsModal').fadeIn();
});
$('.close').click(function(){
    $('.full-shadow').fadeOut();
    $('#settingsModal').fadeOut();
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
$('#send').click(function(){
    var user_id = $('#user_id').val();
    var fd = new FormData();
    var files = $('#file')[0].files;
    
    // Check file selected or not
    if(files.length > 0 )
        fd.append('file',files[0]);

    fd.append('_token', $('#token').val());
    fd.append('nick', $('#nick').val());
    fd.append('content', $('#content').val());

    $.ajax({
        method: 'post',
        url: '/send_msg',
        data: fd,
        contentType: false,
        processData: false
    }).always(function(res){
        var html = '';
        for(var i = 0; i < res.messages.length; i++){
            var msg = res.messages[i];
            var user = res.msg_users;
            var file_html = '';
            if(msg.file_id != 0){
                var file = res.files[msg.file_id][0];
                file_html = '<p class="msg-file"> <a href="/storage/'+file.path+'"> <i class="far fa-file"></i>'+file.filename+' </a> </p> ';
            }
            if(user_id == msg.user_id){
                html += '<div class="msg msg-right mb-12 relative p-2">';
            }else{
                html += '<div class="msg msg-left mb-12 relative p-2">';
            }
            html += '<img src="http://localhost/storage/profiles_miniatures/'+user[msg.user_id].profile_img+'" class="msg-image absolute"/><div class="msg-content"><span class="msg-user_name">'+msg.nick+'</span><p class="msg-content-p" >'+msg.content+'</p><span class="msg-date"></span>'+file_html+'</div></div>';
        }
        $('#messagesList').html(html);
    });

    $('#content').val('');
    $('#file').val('');
    return false;
});
function load_messages(){
    var user_id = $('#user_id').val();
    var msg_switch = false;

    $.ajax({
        method: 'post',
        url:    '/get_msg',
        data:   {_token: $('#token').val()}
    }).always(function(res){
        var html = '';
        for(var i = 0; i < res.messages.length; i++){
            var msg = res.messages[i];
            var user = res.msg_users;
            var file_html = '';
            if(msg.file_id != 0){
                var file = res.files[msg.file_id][0];
                file_html = '<p class="msg-file"> <a href="/storage/'+file.path+'"> <i class="far fa-file"></i>'+file.filename+' </a> </p> ';
            }

            if(user_id == msg.user_id){
                html += '<div class="msg msg-right mb-12 relative p-2">';
            }else{
                html += '<div class="msg msg-left mb-12 relative p-2">';
            }
            html += '<img src="http://localhost/storage/profiles_miniatures/'+user[msg.user_id].profile_img+'" class="msg-image absolute"/><div class="msg-content"><span class="msg-user_name">'+msg.nick+'</span><p class="msg-content-p" >'+msg.content+'</p><span class="msg-date"></span></div>'+file_html+'</div>';
        }
        $('#messagesList').html(html);
        $('#notifySound').get(0).play()
    });

    return false;
}

var worker = new Worker('/js/worker.js');

worker.onmessage = function(e) {
    var newest_id = e.data;
    if(newest_id > $('#newest_id').val()){
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
    worker.postMessage($('#token').val());

    return false;
}, 3000);