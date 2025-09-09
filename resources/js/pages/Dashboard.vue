<template>
    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">
            Hoş geldiniz, {{ user.first_name }} {{ user.last_name }}
        </h1>

        <button
            @click="goToMyComments"
            class="mb-6 px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mr-3">
            Yorumlarım
        </button>
        <button @click="logout" class="mb-6 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Çıkış Yap
        </button>

        <table class="min-w-full bg-white border rounded shadow">
            <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2 border">Başlık</th>
                <th class="px-4 py-2 border">Yayın Tarihi</th>
                <th class="px-4 py-2 border">Yazar</th>
                <th class="px-4 py-2 border">Kategoriler</th>
                <th class="px-4 py-2 border">İçerik</th>
                <th class="px-4 py-2 border">İşlem</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="post in posts" :key="post.id" class="border-b">
                <td class="px-4 py-2 border font-semibold">{{ post.title }}</td>
                <td class="px-4 py-2 border">{{ formatDate(post.published_at) }}</td>
                <td class="px-4 py-2 border">{{ post.user_first_name }} {{ post.user_last_name }}</td>
                <td class="px-4 py-2 border">{{ post.categories.join(', ') }}</td>
                <td class="px-4 py-2 border">{{ post.content }}</td>
                <td class="px-4 py-2 border">
                    <button
                        @click="goToComments(post.id)"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Yorum Yap
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api.js';
import { useRouter } from 'vue-router';

const user = ref(JSON.parse(localStorage.getItem('current_user')) || { first_name: '', last_name: '' });
const posts = ref([]);
const router = useRouter();

onMounted(async () => {
    try {
        const res = await api.get('/posts/published', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        posts.value = res.data || [];
    } catch (e) {
        console.error(e);
        alert('Yazılar yüklenirken hata oluştu.');
    }
});

function formatDate(value) {
    return value ? new Date(value).toLocaleString() : '';
}

const logout = () => {
    localStorage.removeItem('api_token');
    localStorage.removeItem('current_user');
    router.push({ name: 'login' });
};

const goToComments = (postId) => {
    router.push({ name: 'comment_create', params: { id: postId } });
};
const goToMyComments = () => {
    router.push({ name: 'my_comments' });
};
</script>
