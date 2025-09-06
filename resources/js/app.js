// app.js
import axios from 'axios';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import TheMask from 'vue-the-mask';

const app = createApp(App);
app.use(router);
app.use(TheMask);
app.mount('#app');


