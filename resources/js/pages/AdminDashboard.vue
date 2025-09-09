<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const user = ref(window.__CURRENT_USER__ ?? null);

const comments = ref([]);
const approvedComments = ref([]);
const loading = ref(false);
const error = ref(null);

// Çıkış
const logout = async () => {
    try {
        await axios.post('http://127.0.0.1:8000/api/logout', {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
    } catch (e) {
    } finally {
        localStorage.removeItem('api_token');
        window.__CURRENT_USER__ = null;
        router.push({ name: 'login' });
    }
};

// Yorumları getir
const fetchComments = async () => {
    loading.value = true;
    error.value = null;
    try {
        const res = await axios.get('http://127.0.0.1:8000/api/admin/comments', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        comments.value = res.data.filter(c => !c.approved);
        approvedComments.value = res.data.filter(c => c.approved);
    } catch (e) {
        error.value = "Yorumlar yüklenemedi.";
    } finally {
        loading.value = false;
    }
};

// Onayla
const approveComment = async (id) => {
    try {
        const res = await axios.put(`http://127.0.0.1:8000/api/admin/comments/${id}/approve`, {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        const updated = res.data.comment;

        // comments dizisinden çıkar
        comments.value = comments.value.filter(c => c.id !== id);
        // approvedComments dizisinin en sonuna ekle
        approvedComments.value.push(updated);
    } catch (e) {
        alert("Onaylama işlemi başarısız oldu.");
    }
};

// Sil (soft delete) – hem bekleyen hem onaylanan yorumlar için
const deleteComment = async (id) => {
    if (!confirm("Bu yorumu silmek istediğine emin misin?")) return;
    try {
        await axios.delete(`http://127.0.0.1:8000/api/admin/comments/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        // Her iki diziden de çıkar
        comments.value = comments.value.filter(c => c.id !== id);
        approvedComments.value = approvedComments.value.filter(c => c.id !== id);
    } catch (e) {
        alert("Silme işlemi başarısız oldu.");
    }
};

onMounted(fetchComments);
</script>

<template>
    <div class="p-6 space-y-6">
        <!-- Kullanıcı bilgileri ve butonlar -->
        <div class="bg-white p-4 rounded shadow flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Admin Dashboard</h1>
                <p><strong>Hoş geldiniz:</strong> {{ user?.first_name }} {{ user?.last_name }}</p>
                <p><strong>Rol:</strong> {{ user?.role }}</p>
            </div>
            <div class="flex space-x-2">
                <button @click="router.push('/admin/categories')" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Kategori Ekle</button>
                <button class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" @click="router.push({ name: 'admin-posts' })">Yazılar</button>
                <button class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700" @click="logout">Çıkış Yap</button>
            </div>
        </div>

        <!-- Onay Bekleyen Yorumlar -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Onay Bekleyen Yorumlar</h2>
            <div v-if="loading">Yükleniyor...</div>
            <div v-if="error" class="text-red-600">{{ error }}</div>

            <table v-if="comments.length" class="min-w-full border border-gray-200">
                <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Kullanıcı</th>
                    <th class="p-2 border">Yorum</th>
                    <th class="p-2 border">Durum</th>
                    <th class="p-2 border">İşlem</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="comment in comments" :key="comment.id">
                    <td class="p-2 border">{{ comment.id }}</td>
                    <td class="p-2 border">{{ comment.user?.first_name }} {{ comment.user?.last_name }}</td>
                    <td class="p-2 border">{{ comment.content }}</td>
                    <td class="p-2 border">Bekliyor</td>
                    <td class="p-2 border text-center">
                        <button class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 mr-2" @click="approveComment(comment.id)">Onayla</button>
                        <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700" @click="deleteComment(comment.id)">Sil</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <p v-else class="text-gray-600">Onay bekleyen yorum yok.</p>
        </div>

        <!-- Onaylanan Yorumlar -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Onaylanan Yorumlar</h2>
            <table v-if="approvedComments.length" class="min-w-full border border-gray-200">
                <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Kullanıcı</th>
                    <th class="p-2 border">Yorum</th>
                    <th class="p-2 border">Durum</th>
                    <th class="p-2 border">İşlem</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="comment in approvedComments" :key="comment.id">
                    <td class="p-2 border">{{ comment.id }}</td>
                    <td class="p-2 border">{{ comment.user?.first_name }} {{ comment.user?.last_name }}</td>
                    <td class="p-2 border">{{ comment.content }}</td>
                    <td class="p-2 border">Onaylandı</td>
                    <td class="p-2 border text-center">
                        <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700" @click="deleteComment(comment.id)">Sil</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <p v-else class="text-gray-600">Henüz onaylanan yorum yok.</p>
        </div>
    </div>
</template>

<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
}
</style>
