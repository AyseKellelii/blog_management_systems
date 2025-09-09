import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

// Sayfalar
import Register from '../pages/Auth/Register.vue';
import Login from '../pages/Auth/Login.vue';
import Dashboard from '../pages/Dashboard.vue';
import AdminDashboard from '../pages/AdminDashboard.vue';
import AuthorDashboard from '../pages/AuthorDashboard.vue';
import CategoryManagement from '../pages/CategoryManagement.vue';
import CommentCreate from '../pages/CommentCreate.vue';
import MyComments from "../pages/MyComments.vue";
import PostsPage from '../pages/PostsPage.vue';
import AuthorComments from '../pages/AuthorComments.vue';

const routes = [
    { path: '/', redirect: '/register' },
    { path: '/register', name: 'register', component: Register },
    { path: '/login', name: 'login', component: Login },
    { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { auth: true } },
    { path: '/admin', name: 'admin', component: AdminDashboard, meta: { auth: true, role: 'admin' } },
    { path: '/author', name: 'author', component: AuthorDashboard, meta: { auth: true, role: 'author' } },
    { path: '/category_management', name: 'category_management', component: CategoryManagement, meta: { auth: true, role: 'author' }},
    {path: '/admin/categories', name: 'admin-categories', component: CategoryManagement, meta: { auth: true, role: 'admin' }},
    { path: '/posts/:id/comments', name: 'comment_create', component: CommentCreate },
    { path: '/my-comments', name: 'my_comments', component: MyComments, meta: { auth: true } },
    {path: '/admin', name: 'admin-dashboard', component: AdminDashboard,},
    {path: '/admin/posts', name: 'admin-posts', component: PostsPage,},
    {path: '/author/comments', name: 'author.comments', component: AuthorComments, meta: { requiresAuth: true }},
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
