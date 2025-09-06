<template>
    <div class="p-6">
        <h1 class="text-2xl mb-4">Dashboard</h1>
        <div class="bg-white p-4 rounded shadow">
            <p><strong>Hoş geldin:</strong> {{ user?.first_name }} {{ user?.last_name }}</p>
            <p><strong>Rol:</strong> {{ user?.role }}</p>
            <button class="mt-4 px-3 py-2 bg-red-600 text-white rounded" @click="logout">Çıkış Yap</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const user = ref(window.__CURRENT_USER__ ?? null);

const logout = async () => {
    try {
        await axios.post('http://127.0.0.1:8000/api/logout', {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
    } catch (e) {
        // ignore
    } finally {
        localStorage.removeItem('api_token');
        window.__CURRENT_USER__ = null;
        router.push({ name: 'login' });
    }
};
</script>
