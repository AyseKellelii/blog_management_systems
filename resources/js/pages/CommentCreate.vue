<template>
    <div class="p-6 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold mb-2">Başlık: {{ post.title }}</h2>
        <div class="mb-4 p-4 border rounded bg-gray-50">
            <p><strong>İçerik:</strong> {{ post.content }}</p>
            <p><strong>Yazar:</strong> {{ post.user_first_name }} {{ post.user_last_name }}</p>
            <p><strong>Yayın Tarihi: </strong>
                 {{ formatDate(post.published_at) }}
            </p>
        </div>

        <h3 class="text-xl font-semibold mb-2">Yorumlar</h3>
        <div v-if="comments.length === 0" class="text-gray-500 mb-2">Henüz yorum yok.</div>
        <div v-for="comment in comments" :key="comment.id" class="mb-2 p-2 border rounded">
            <p>{{ comment.content }}</p>
            <p class="text-gray-500 text-sm">
                - {{ comment.user.first_name }} {{ comment.user.last_name }}
            </p>
        </div>

        <textarea
            v-model="icerik"
            class="w-full border p-2 rounded mb-4"
            rows="5"
            placeholder="Yorumunuzu yazın..."
        ></textarea>
        <button
            @click="submitComment"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
            Gönder
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api.js';

const route = useRoute();
const post = ref({});
const comments = ref([]);
const icerik = ref('');

const formatDate = (value) => (value ? new Date(value).toLocaleString() : '');

// Tek API çağrısı ile yazı ve yorumları çek
const fetchPost = async () => {
    try {
        const res = await api.get(`/posts/${route.params.id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` },
        });
        post.value = res.data;
        comments.value = res.data.comments || [];
    } catch (e) {
        console.error(e);
        alert('Veriler yüklenirken hata oluştu.');
    }
};

onMounted(fetchPost);

const submitComment = async () => {
    if (!icerik.value.trim()) {
        alert('Yorum boş olamaz.');
        return;
    }

    try {
        await api.post(
            `/posts/${route.params.id}/comments`,
            {
                icerik: icerik.value,
                post_id: route.params.id
            },
            { headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` } }
        );
        icerik.value = '';

        // Yorum başarılı uyarısı
        alert('Yorum başarıyla gönderildi!');

        fetchPost(); // Yorum eklendikten sonra listeyi güncelle
    } catch (e) {
        console.error(e);
        alert('Yorum gönderilirken hata oluştu.');
    }

};
</script>
