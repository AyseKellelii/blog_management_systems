<template>
    <div class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded shadow w-full max-w-lg">
            <h2 class="text-xl font-bold mb-4">{{ editingPost ? 'Yazıyı Düzenle' : 'Yeni Yazı' }}</h2>

            <!-- Başlık -->
            <div class="mb-2">
                <label class="block mb-1">Başlık</label>
                <input v-model="editingPostData.title" type="text" class="border p-1 rounded w-full"/>
            </div>

            <!-- İçerik -->
            <div class="mb-2">
                <label class="block mb-1">İçerik</label>
                <textarea v-model="editingPostData.content" class="border p-1 rounded w-full h-32"></textarea>
            </div>

            <!-- Tek Kategori Seçimi -->
            <div class="mb-2">
                <label class="block mb-1">Kategori</label>
                <select v-model="editingPostData.category_ids[0]" class="border p-1 rounded w-full">
                    <option value="">-- Seçiniz --</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
            </div>

            <!-- Yayın Tarihi -->
            <div class="mb-2">
                <label class="block mb-1">Yayın Tarihi</label>
                <input v-model="editingPostData.published_at" type="datetime-local" class="border p-1 rounded w-full"/>
            </div>


            <!-- Kapak Resmi -->
            <div class="mb-2">
                <label class="block mb-1">Kapak Resmi</label>
                <div class="flex items-center gap-4">

                    <input type="file" @change="handleImageUpload" class="border p-1 rounded w-full"/>
                </div>
            </div>

            <!-- Durum -->
            <div class="mb-4">
                <label class="block mb-1">Durum</label>
                <select v-model="editingPostData.status" class="border p-1 rounded w-full">
                    <option value="draft">Taslak</option>
                    <option value="published">Yayınla</option>
                </select>
            </div>

            <!-- Butonlar -->
            <div class="flex justify-end gap-2">
                <button @click="$emit('close')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">İptal</button>
                <button @click="savePost" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Kaydet</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import api from '../api.js';

const props = defineProps({
    editingPost: Boolean,  // güncelleme mi?
    postData: Object       // gönderilen post verisi
});
const emit = defineEmits(['close', 'save']);

const editingPostData = reactive({
    id: null,
    title: '',
    content: '',
    category_ids: [],
    cover_image_file: null,
    status: 'draft',
    published_at: '',
});

const categories = ref([]);

// Modal açıldığında postData varsa formu doldur
onMounted(async () => {
    if (props.postData) {
        Object.assign(editingPostData, props.postData);

        if (props.postData.published_at) {
            const d = new Date(props.postData.published_at);
            editingPostData.published_at = d.toISOString().slice(0, 16);
        }
    }

    try {
        const res = await api.get('/categories', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        categories.value = res.data || [];
    } catch (e) {
        console.error("Kategoriler yüklenirken hata:", e);
    }
});

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) editingPostData.cover_image_file = file;
};

async function savePost() {
    try {
        const formData = new FormData();
        formData.append('title', editingPostData.title);
        formData.append('icerik', editingPostData.content);
        formData.append('status', editingPostData.status);
        formData.append('published_at', editingPostData.published_at || '');
        editingPostData.category_ids.forEach(id => formData.append('category_ids[]', id));
        if (editingPostData.cover_image_file) {
            formData.append('cover_image', editingPostData.cover_image_file);
        }

        if (props.editingPost && editingPostData.id) {
            // ✅ Güncelleme için _method: PUT
            formData.append('_method', 'PUT');
            await api.post(`/posts/${editingPostData.id}`, formData, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`,
                    'Content-Type': 'multipart/form-data',
                }
            });
        } else {
            // ✅ Yeni kayıt
            await api.post('/posts', formData, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('api_token')}`,
                    'Content-Type': 'multipart/form-data',
                }
            });
        }

        emit('save');  // dashboard’da fetchPosts() çalışacak
        emit('close');
    } catch (e) {
        console.error(e.response?.data || e);
        alert(JSON.stringify(e.response?.data?.errors || e.response?.data || 'Kaydetme sırasında hata oluştu.'));
    }
}
</script>
