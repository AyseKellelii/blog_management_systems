import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

// Sayfalar
import Register from '../pages/Auth/Register.vue';
import Login from '../pages/Auth/Login.vue';
import Dashboard from '../pages/Dashboard.vue';
import AdminDashboard from '../pages/AdminDashboard.vue';
import AuthorDashboard from '../pages/AuthorDashboard.vue';
import CategoryManagement from '../pages/CategoryManagement.vue';

const routes = [
    { path: '/', redirect: '/register' },
    { path: '/register', name: 'register', component: Register },
    { path: '/login', name: 'login', component: Login },
    { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { auth: true, role: 'user' } },
    { path: '/admin', name: 'admin', component: AdminDashboard, meta: { auth: true, role: 'admin' } },
    { path: '/author', name: 'author', component: AuthorDashboard, meta: { auth: true, role: 'author' } },
    {
        path: '/category_management',
        name: 'category_management',
        component: CategoryManagement,
        meta: { auth: true, role: 'author' }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('api_token');

    if (to.meta.auth && !token) return next({ name: 'login' });

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
            return next({ name: 'login' });
        }
    }

    if (to.meta.role && window.__CURRENT_USER__) {
        if (window.__CURRENT_USER__.role !== to.meta.role) {
            switch (window.__CURRENT_USER__.role) {
                case 'admin':
                    return next({ name: 'admin' });
                case 'author':
                    return next({ name: 'author' });
                default:
                    return next({ name: 'dashboard' });
            }
        }
    }

    next();
});

export default router;
