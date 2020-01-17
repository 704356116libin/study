
// window._ = require('lodash');
// window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.$ = window.jQuery = require('jquery');

  require('bootstrap');
} catch (e) { }



/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.Cookies = require('js-cookie'); //引入cookie
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = localStorage.getItem("access_token");

if (token) { // 配置全局token
  window.axios.defaults.headers['Authorization'] = 'Bearer ' + token;
  // if (localStorage.getItem("rememberme") === "yes") { //记住我
  //   window.axios.defaults.headers['Authorization'] = 'Bearer ' + token;
  // } else {
  //   if (Cookies.get('rememberme')) { //
  //     window.axios.defaults.headers['Authorization'] = 'Bearer ' + token;
  //   } else {
  //     if (window.location.pathname == "register") {
  //       window.location.href = "/register"
  //     } else if (window.location.pathname !== "/login") {
  //       window.location.href = "/login"
  //     }
  //   }
  // }
}
// else {
//   if (window.location.pathname !== "/register" && window.location.pathname !== "/login") {
//     window.location.href = "/login"
//   }
// }

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
