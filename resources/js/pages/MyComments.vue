<template>
    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Yaptığım Yorumlar</h1>

        <button
            @click="$router.push({ name: 'dashboard' })"
            class="mb-4 px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            Geri
        </button>

        <h3 class="text-xl font-semibold mb-2">Yorumlarım</h3>
        <div v-if="comments.length === 0" class="text-gray-500 mb-2">
            Henüz yorum yapmadınız.
        </div>

        <div v-for="comment in comments" :key="comment.id" class="mb-2 p-4 border rounded bg-gray-50">
            <p>{{ comment.content }}</p>
            <p class="text-gray-500 text-sm">
                - {{ comment.user.first_name }} {{ comment.user.last_name }}
                <span v-if="!comment.approved" class="text-red-500">(Beklemede)</span>
                <span v-else class="text-green-600">(Onaylandı)</span>
            </p>

            <div class="flex justify-between items-center mt-2">
                <p class="text-sm text-gray-400">
                    Yazı: {{ comment.post.title }}
                </p>
                <div class="flex gap-2">
                    <button
                        @click="goToPost(comment.post.id)"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Yazıya Git
                    </button>

                    <!-- Düzenle Butonu -->
                    <button
                        @click="startEdit(comment)"
                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Düzenle
                    </button>

                    <!-- Sil Butonu -->
                    <button
                        @click="deleteComment(comment.id)"
                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                        Sil
                    </button>
                </div>
            </div>
        </div>

        <!-- Düzenleme Modalı -->
        <div v-if="editingComment" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded shadow w-96">
                <h2 class="text-lg font-bold mb-4">Yorumu Düzenle</h2>
                <textarea v-model="editContent" rows="4" class="w-full p-2 border rounded mb-4"></textarea>
                <div class="flex justify-end gap-2">
                    <button @click="editingComment=null" class="px-3 py-1 bg-gray-400 text-white rounded">
                        Vazgeç
                    </button>
                    <button @click="updateComment" class="px-3 py-1 bg-green-600 text-white rounded">
                        Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import api from '../api.js';
import {useRouter} from 'vue-router';

const comments = ref([]);
const router = useRouter();

const editingComment = ref(null);
const editContent = ref("");

const fetchComments = async () => {
    try {
        const res = await api.get('/my-comments', {
            headers: {Authorization: `Bearer ${localStorage.getItem('api_token')}`}
        });
        comments.value = res.data;
    } catch (e) {
        console.error(e);
        alert('Yorumlar yüklenirken hata oluştu.');
    }
};

onMounted(fetchComments);

const goToPost = (postId) => {
    router.push({name: 'comment_create', params: {id: postId}});
};

// Düzenleme başlat
const startEdit = (comment) => {
    editingComment.value = comment;
    editContent.value = comment.content;
};

// Yorum güncelle
const updateComment = async () => {
    try {
        await api.put(`/comments/${editingComment.value.id}`, {
            icerik: editContent.value
        }, {
            headers: {Authorization: `Bearer ${localStorage.getItem('api_token')}`}
        });
        alert("Yorum güncellendi, admin onayı bekliyor.");
        editingComment.value = null;
        fetchComments();
    } catch (e) {
        console.error(e);
        alert("Yorum güncellenirken hata oluştu.");
    }
};

// Yorum sil
const deleteComment = async (id) => {
    if (!confirm("Bu yorumu silmek istediğinize emin misiniz?")) return;
    try {
        await api.delete(`/comments/${id}/delete-own`, {
            headers: {Authorization: `Bearer ${localStorage.getItem('api_token')}`}
        });
        comments.value = comments.value.filter(c => c.id !== id);
    } catch (e) {
        console.error(e);
        alert("Yorum silinirken hata oluştu.");
    }
};
</script>
