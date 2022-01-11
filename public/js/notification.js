/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************!*\
  !*** ./resources/js/notification.js ***!
  \**************************************/
onmessage = function onmessage(e) {
  var ajax = new XMLHttpRequest();
  ajax.responseType = 'json';

  switch (e.data.name) {
    case "notify_room_message":
      ajax.open('GET', '/get_notify_data/' + e.data.room);

      ajax.onload = function () {
        var res = ajax.response;
        var notification = new Notification("Użytkownik " + res.user + " wysłał wiadomość do pokoju " + res.room);
      };

      ajax.send('_token=' + e.data.token);
      break;

    case "check_messages":
      ajax.open('GET', '/get_notify_data');

      ajax.onload = function () {
        var res = ajax.response;
        var notification = new Notification("Użytkownik " + res.user + " wysłał wiadomość do pokoju " + res.room);
      };

      ajax.send('_token=' + e.data.token);
      break;

    default:
      console.error("Unknown message:", e.data.name);
  }

  postMessage('Receiver');
};
/******/ })()
;