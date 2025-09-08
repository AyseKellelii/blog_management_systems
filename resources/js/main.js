import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import '../css/app.css'; // DOĞRU YOL
import api from './api.js';
import TheMask from 'vue-the-mask';

const app = createApp(App);

app.use(TheMask);
app.config.globalProperties.$api = api;
app.use(router);
app.mount('#app');
