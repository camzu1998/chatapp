/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************!*\
  !*** ./resources/js/worker.js ***!
  \********************************/
onmessage = function onmessage(e) {
  var ajax = new XMLHttpRequest();

  ajax.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log('Newest id: ' + this.responseText);
      postMessage(this.responseText);
    }
  };

  ajax.open('POST', '/get_newest_id');
  ajax.send('_token=' + e.data);
};
/******/ })()
;