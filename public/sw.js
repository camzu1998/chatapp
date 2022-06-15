/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************!*\
  !*** ./resources/js/sw.js ***!
  \****************************/
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').then(function (r) {
    console.log('ServiceWorker zarejestrowany.');
  })["catch"](function (e) {
    console.log('Ups! Błąd przy rejestracji ServiceWorkera! ' + e);
  });
}

var CACHE_NAME = 'init_cache';
var urlsToCache = ['/css/app.css', '/storage/sounds/mmm-2-tone-sexy.mp3'];
self.addEventListener('install', function (event) {
  // Perform install steps
  event.waitUntil(caches.open(CACHE_NAME).then(function (cache) {
    console.log('Opened cache');
    return cache.addAll(urlsToCache);
  }));
}); // self.addEventListener('fetch', function(event) {
//     event.respondWith(
//         caches.match(event.request)
//             .then(function(response) {
//                 // Cache hit - return response
//                 if (response) {
//                     return response;
//                 }
//                 return fetch(event.request).then(
//                     function(response) {
//                         // Check if we received a valid response
//                         if(!response || response.status !== 200 || response.type !== 'basic') {
//                             return response;
//                         }
//                         // IMPORTANT: Clone the response. A response is a stream
//                         // and because we want the browser to consume the response
//                         // as well as the cache consuming the response, we need
//                         // to clone it so we have two streams.
//                         // var responseToCache = response.clone();
//                         // caches.open(CACHE_NAME)
//                         //     .then(function(cache) {
//                         //         cache.put(event.request, responseToCache);
//                         //     });
//                         return response;
//                     }
//                 );
//             }
//         )
//     );
// });

self.addEventListener('activate', function (event) {
  var cacheAllowlist = ['init_cache'];
  event.waitUntil(caches.keys().then(function (cacheNames) {
    return Promise.all(cacheNames.map(function (cacheName) {
      if (cacheAllowlist.indexOf(cacheName) === -1) {
        return caches["delete"](cacheName);
      }
    }));
  }));
});
/******/ })()
;