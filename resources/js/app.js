/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('list-all-ips-by-server', require('./components/SoYouStart/ListAllIpsByServer.vue').default);
Vue.component('reboot-server-button', require('./components/SoYouStart/RebootServerButton.vue').default);
Vue.component('dedicated-server-details', require('./components/SoYouStart/DedicatedServerDetails.vue').default)
Vue.component('ip-reverse', require('./components/SoYouStart/IpReverse.vue').default)
Vue.component('virtual-mac', require('./components/SoYouStart/VirtualMac.vue').default)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
