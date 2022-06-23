import Vue from 'vue';

import store from './state'

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';
Vue.use(VueAxios, axios);
Vue.prototype.$axios = axios

import App from './App.vue';
import Home from './components/Home.vue'
const routes = [
    {
        name: 'Home',
        path: '/',
        component: Home
    }
  ];

  
const router = new VueRouter({ routes: routes, mode: 'history'});
new Vue(Vue.util.extend({ store, router }, App)).$mount('#app');