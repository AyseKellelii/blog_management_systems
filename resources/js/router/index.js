import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import Register from '../Pages/Auth/Register.vue';
import Login from '../Pages/Auth/Login.vue';
import Dashboard from '../Pages/Dashboard.vue';

const routes = [
    { path: '/', redirect: '/register' }, // Proje açılınca register sayfası
    { path: '/register', name: 'register', component: Register },
    { path: '/login', name: 'login', component: Login },
    { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { auth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Route guard: auth gerektiren sayfalar için
router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('api_token');

    // Auth gerektiren sayfaya token yoksa login sayfasına yönlendir
    if (to.meta.auth && !token) return next({ name: 'login' });

    // Token varsa kullanıcıyı yükle
    if (token && !window.__USER_LOADED__) {
        try {
            const res = await axios.get('http://127.0.0.1:8000/api/me', {
                headers: { Authorization: `Bearer ${token}` },
            });
            window.__CURRENT_USER__ = res.data;
            window.__USER_LOADED__ = true;
        } catch (e) {
            localStorage.removeItem('api_token');
            window.__USER_LOADED__ = true;
        }
    }

    next();
});

export default router;
