/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Buefy from 'buefy'
import 'buefy/dist/buefy.css'
import CKEditor from '@ckeditor/ckeditor5-vue2';

Vue.use(Buefy)
Vue.use(CKEditor);
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('ticket-created-component', require('./components/TicketCreatedComponent.vue').default);
Vue.component('ticket-table-all-component', require('./components/TicketTableAllComponent.vue').default);
Vue.component('ticket-table-all-archive-component', require('./components/TicketTableAllArchiveComponent.vue').default);
Vue.component('ticket-table-outgoing-component', require('./components/TicketTableOutgoingComponent.vue').default);
Vue.component('people-table-all-component', require('./components/PeopleTableAllComponent.vue').default);
Vue.component('people-table-all-ticket-component', require('./components/PeopleTableAllTicketComponent.vue').default);
Vue.component('ticket-table-you-component', require('./components/TicketTableYouComponent.vue').default);
Vue.component('ticket-for-approval-component', require('./components/TicketForApprovalComponent.vue').default);
Vue.component('ticket-view-component', require('./components/TicketViewComponent.vue').default);
Vue.component('ticket-show-component', require('./components/TicketShowComponent.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
