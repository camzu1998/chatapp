/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************!*\
  !*** ./resources/js/worker.js ***!
  \********************************/
onmessage = function onmessage(e) {
  var ajax = new XMLHttpRequest();
  var data = e.data;

  if (data[1] != undefined) {
    ajax.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        console.log('Newest id: ' + this.responseText);
        postMessage(this.responseText);
      }
    };

    ajax.open('GET', '/get_newest_id/' + data[1]);
    ajax.send('_token=' + data[0]);
  }
};
/******/ })()
;