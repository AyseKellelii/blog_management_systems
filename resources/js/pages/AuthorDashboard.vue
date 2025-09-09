<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <!-- Navbar -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Yazar Paneli</h1>
            <button @click="logout" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                Çıkış Yap
            </button>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-3">
                <button @click="openCreatePost" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Yeni Yazı
                </button>
                <button @click="goCategoryManagement" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    Kategori Yönetimi
                </button>
            </div>
            <div class="text-gray-600 text-sm">
                Hoş geldin, {{ currentUser.first_name }} {{ currentUser.last_name }}
            </div>
        </div>

        <!-- Posts Table -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left whitespace-nowrap">Başlık</th>
                    <th class="px-6 py-3 text-left whitespace-nowrap">Kategori</th>
                    <th class="px-6 py-3 text-left whitespace-nowrap">Durum</th>
                    <th class="px-6 py-3 text-left whitespace-nowrap">Yayın Tarihi</th>
                    <th class="px-6 py-3 text-left whitespace-nowrap">Aksiyon</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="post in posts" :key="post.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ post.title || 'Başlık yok' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ post.categories?.map(c => c.name).join(', ') || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="post.status === 'published' ? 'text-green-600' : 'text-gray-600'">
                                {{ post.status === 'published' ? 'Yayınlandı' : 'Taslak' }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ post.published_at ? new Date(post.published_at).toLocaleString() : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                        <button @click="openEditPost(post)" class="px-2 py-1 bg-yellow-400 text-white rounded text-sm">Düzenle</button>
                        <button @click="togglePublish(post)" class="px-2 py-1 bg-blue-500 text-white rounded text-sm">
                            {{ post.status === 'published' ? 'Taslağa Al' : 'Yayınla' }}
                        </button>
                        <button @click="softDelete(post)" class="px-2 py-1 bg-red-500 text-white rounded text-sm">Sil</button>
                    </td>
                </tr>
                <tr v-if="posts.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Henüz yazı yok.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center gap-2">
            <button v-if="page > 1" @click="fetchPosts(page - 1)" class="px-3 py-1 border rounded">Önceki</button>
            <button v-if="posts.length === per_page" @click="fetchPosts(page + 1)" class="px-3 py-1 border rounded">Sonraki</button>
        </div>

        <!-- Post Modal -->
        <PostModal
            v-if="showPostModal"
            :editingPost="editingPost"
            :postData="editingPostData"
            @close="showPostModal=false"
            @save="fetchPosts"
        />
    </div>
</template>

<script setup>
import {ref} from 'vue';
import {useRouter} from 'vue-router';
import api from '../api.js';
import PostModal from './PostModal.vue';

const router = useRouter();
const currentUser = ref(JSON.parse(localStorage.getItem('current_user')) ?? {first_name: '', last_name: ''});

const posts = ref([]);
const page = ref(1);
const per_page = 6;
const showPostModal = ref(false);
const editingPost = ref(false);
const editingPostData = ref(null);

const fetchPosts = async (p = 1) => {
    page.value = p;
    try {
        const res = await api.get(`/author/posts?page=${p}&per_page=${per_page}`, {
            headers: {Authorization: `Bearer ${localStorage.getItem('api_token')}`}
        });
        posts.value = res.data.data || res.data || [];
    } catch {
        posts.value = [];
    }
};

const openCreatePost = () => {
    editingPost.value = false;
    editingPostData.value = null;
    showPostModal.value = true;
};

const openEditPost = (post) => {
    editingPost.value = true;
    editingPostData.value = {
        ...post,
        category_ids: post.categories?.map(c => c.id) || [],
        published_at: post.published_at ? new Date(post.published_at).toISOString().slice(0,16) : ''
    };
    showPostModal.value = true;
};

const togglePublish = async (post) => {
    try {
        const res = await api.patch(`/posts/${post.id}/toggle-status`, null, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });

        // Post objesini güncelle
        Object.assign(post, res.data);

    } catch (e) {
        console.error('Yayın durumu değiştirilemedi: ', e);
        alert('Yayın durumu değiştirilemedi.');
    }
};
const softDelete = async (post) => {
    try {
        await api.delete(`/posts/${post.id}`, {
            headers: {Authorization: `Bearer ${localStorage.getItem('api_token')}`}
        });
        posts.value = posts.value.filter(p => p.id !== post.id);
    } catch {}
};

const goCategoryManagement = () => router.push({name: 'category_management'});

const handlePostSave = (newPost) => {
    if (editingPost.value) {
        const idx = posts.value.findIndex(p => p.id === newPost.id);
        if (idx !== -1) posts.value[idx] = newPost;
    } else {
        posts.value.unshift(newPost);
    }
};

const logout = async () => {
    try {
        await api.post('/logout', null, {
            headers: {Authorization: `Bearer ${localStorage.getItem('api_token')}`}
        });
        localStorage.removeItem('api_token');
        localStorage.removeItem('current_user');
        router.push({name: 'login'});
    } catch {
    }
};

fetchPosts();
</script>
