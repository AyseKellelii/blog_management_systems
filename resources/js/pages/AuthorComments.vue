<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <h1 class="text-2xl font-bold mb-4">Yazılarınıza Gelen Yorumlar</h1>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Yorum Yapan</th>
                    <th class="px-6 py-3 text-left">Yorum</th>
                    <th class="px-6 py-3 text-left">Yazı Başlığı</th>
                    <th class="px-6 py-3 text-left">Durum</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="comment in comments" :key="comment.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ comment.user.first_name }} {{ comment.user.last_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ comment.content }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ comment.post.title }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
  <span :class="comment.approved ? 'text-green-600' : 'text-red-600'">
    {{ comment.approved ? 'Onaylı' : 'Beklemede' }}
  </span>
                    </td>
                </tr>
                <tr v-if="comments.length === 0">
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Henüz yorum yok.
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api.js';

const comments = ref([]);

const fetchComments = async () => {
    try {
        const res = await api.get('/author/comments', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        comments.value = res.data;
    } catch (e) {
        console.error('Yorumlar yüklenemedi.', e);
        comments.value = [];
    }
};

onMounted(() => {
    fetchComments();
});
</script>
