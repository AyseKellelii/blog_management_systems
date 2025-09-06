resources/js/main.js
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './styles.css'; // tailwind entry (app.css)
import { VueMaskDirective } from '@mask/vue-the-mask';

const app = createApp(App);

// global directive for mask
app.directive('mask', VueMaskDirective);

app.use(router);
app.mount('#app');
