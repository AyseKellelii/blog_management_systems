<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const posts = ref([]);
const loading = ref(false);
const error = ref(null);

const fetchPosts = async () => {
    loading.value = true;
    error.value = null;
    try {
        const res = await axios.get('http://127.0.0.1:8000/api/admin/posts', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        posts.value = res.data;
    } catch (e) {
        error.value = "Yazılar yüklenemedi.";
    } finally {
        loading.value = false;
    }
};

const goBack = () => {
    router.push('/admin');
};

onMounted(fetchPosts);
</script>

<template>
    <div class="p-6">
        <!-- Geri Butonu -->
        <button @click="goBack" class="mb-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
            ← Geri
        </button>

        <h1 class="text-2xl font-bold mb-4">Yazılar</h1>

        <div v-if="loading">Yükleniyor...</div>
        <div v-if="error" class="text-red-600">{{ error }}</div>

        <table v-if="posts.length" class="min-w-full border border-gray-200">
            <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Başlık</th>
                <th class="p-2 border">Yazar</th>
                <th class="p-2 border">Durum</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="post in posts" :key="post.id">
                <td class="p-2 border">{{ post.id }}</td>
                <td class="p-2 border">{{ post.title }}</td>
                <td class="p-2 border">{{ post.user_first_name }} {{ post.user_last_name }}</td>
                <td class="p-2 border">{{ post.status }}</td>
            </tr>
            </tbody>
        </table>

        <p v-else class="text-gray-600">Henüz yazı yok.</p>
    </div>
</template>
