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

        if (res.status == true) {
          var notification = new Notification("Użytkownik " + res.user + " wysłał wiadomość do pokoju " + res.room);
        }
      };

      ajax.send('_token=' + e.data.token);
      break;

    case "check_messages":
      ajax.open('GET', '/get_notify_data');

      ajax.onload = function () {
        var res = ajax.response;

        if (res.length != 0) {
          res.forEach(function (el, index) {
            var notification = new Notification("Użytkownik " + el.user + " wysłał wiadomość do pokoju " + el.room);
          });
        }
      };

      ajax.send('_token=' + e.data.token);
      break;

    case "notification":
      var notification = new Notification("Hi there :)");
      break;

    default:
      console.error("Unknown message:", e.data.name);
  }

  postMessage('Receiver');
};
/******/ })()
;