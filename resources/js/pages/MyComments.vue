<template>
    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">
            Hoş geldiniz, {{ user.first_name }} {{ user.last_name }}
        </h1>

        <button
            @click="goToDashboard"
            class="mb-6 px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mr-3"
        >
            Dashboard
        </button>
        <button
            @click="logout"
            class="mb-6 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
            Çıkış Yap
        </button>

        <div v-if="loading" class="text-gray-600">Yükleniyor...</div>
        <div v-if="error" class="text-red-600">{{ error }}</div>

        <div v-if="comments.length === 0 && !loading" class="text-gray-600 mt-4">
            Henüz yorum yapmadınız.
        </div>

        <table v-if="comments.length" class="min-w-full bg-white border rounded shadow">
            <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2 border">Yazı Başlığı</th>
                <th class="px-4 py-2 border">Yorum</th>
                <th class="px-4 py-2 border">Durum</th>
                <th class="px-4 py-2 border">Tarih</th>
                <th class="px-4 py-2 border">İşlem</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="comment in comments" :key="comment.id" class="border-b">
                <td class="px-4 py-2 border">{{ comment.post_title }}</td>
                <td class="px-4 py-2 border">{{ comment.content }}</td>
                <td class="px-4 py-2 border">
            <span
                :class="comment.approved ? 'text-green-600 font-bold' : 'text-yellow-600 font-bold'"
            >
              {{ comment.approved ? 'Onaylandı' : 'Onay Bekliyor' }}
            </span>
                </td>
                <td class="px-4 py-2 border">{{ formatDate(comment.created_at) }}</td>
                <td class="px-4 py-2 border">
                    <button
                        @click="editComment(comment)"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 mr-2"
                    >
                        Düzenle
                    </button>
                    <button
                        @click="deleteComment(comment.id)"
                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                    >
                        Sil
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

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('current_user')) || { first_name: '', last_name: '' });
const comments = ref([]);
const loading = ref(false);
const error = ref(null);

const fetchComments = async () => {
    loading.value = true;
    error.value = null;
    try {
        const res = await api.get('/my-comments', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` },
        });
        comments.value = res.data || [];
    } catch (e) {
        console.error(e);
        error.value = 'Yorumlar yüklenirken hata oluştu.';
    } finally {
        loading.value = false;
    }
};

const formatDate = (value) => (value ? new Date(value).toLocaleString() : '');

const logout = () => {
    localStorage.removeItem('api_token');
    localStorage.removeItem('current_user');
    router.push({ name: 'login' });
};

const goToDashboard = () => {
    router.push({ name: 'dashboard' });
};

const editComment = (comment) => {
    const newContent = prompt('Yorumunuzu düzenleyin:', comment.content);
    if (newContent !== null && newContent.trim() !== '') {
        api
            .put(`/comments/${comment.id}`, { icerik: newContent }, { headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` } })
            .then((res) => {
                comment.content = res.data.comment.content;
                comment.approved = false; // düzenleme sonrası tekrar onay bekler
                alert('Yorum güncellendi, admin onayı bekliyor.');
            })
            .catch((err) => {
                console.error(err);
                alert('Yorum güncellenemedi.');
            });
    }
};

const deleteComment = (id) => {
    if (!confirm('Bu yorumu silmek istediğinize emin misiniz?')) return;
    api
        .delete(`/comments/${id}/delete-own`, { headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` } })
        .then(() => {
            comments.value = comments.value.filter((c) => c.id !== id);
            alert('Yorum silindi.');
        })
        .catch((err) => {
            console.error(err);
            alert('Yorum silinemedi.');
        });
};

onMounted(fetchComments);
</script>

<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
}
</style>
