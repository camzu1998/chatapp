onmessage=function(e){var a=new XMLHttpRequest;switch(a.responseType="json",e.data.name){case"notify_room_message":a.open("GET","/get_notify_data/"+e.data.room),a.onload=function(){var e=a.response;1==e.status&&postMessage(e)},a.send("_token="+e.data.token);break;case"check_messages":a.open("GET","/get_notify_data"),a.onload=function(){var e=a.response;0!=e.length&&e.forEach((function(a,n){postMessage(e)}))},a.send("_token="+e.data.token);break;case"notification":postMessage("notification");break;default:console.error("Unknown message:",e.data.name)}};