onmessage = function(e) {
    var ajax = new XMLHttpRequest();
    ajax.responseType = 'json';
    switch(e.data.name) {
        case "notify_room_message": 
            ajax.open('GET', '/get_notify_data/'+e.data.room);
            ajax.onload  = function() {
                var res = ajax.response;
                if(res.status == true){
                    postMessage(res);
                }
             };
            ajax.send('_token='+e.data.token);          
            break;
        case "check_messages":
            ajax.open('GET', '/get_notify_data');
            ajax.onload  = function() {
                var res = ajax.response;
                if(res.length != 0 && res.sum_unreaded != 0){
                    postMessage(res);
                }
             };
            ajax.send('_token='+e.data.token);   
            break;
        case "notification":
            postMessage('notification');
            break;
        default:
          console.error("Unknown message:", e.data.name);
    }
}