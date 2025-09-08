<template>
    <div class="p-6 min-h-screen bg-gray-100">
        <h1 class="text-2xl font-bold mb-4">Kategori Yönetimi</h1>

        <router-link to="/author" class="mb-4 inline-block text-blue-600 hover:underline">
            Geri
        </router-link>

        <!-- Kategoriler Tablosu -->
        <table class="min-w-full bg-white rounded shadow mb-4">
            <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Kategori Adı</th>
                <th class="px-4 py-2">Slug</th>
                <th class="px-4 py-2">Oluşturma Tarihi</th>
                <th class="px-4 py-2">İşlemler</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="cat in categories" :key="cat.id">
                <td class="border px-4 py-2">{{ cat.id }}</td>
                <td class="border px-4 py-2">
                    <input v-model="cat.name" class="border p-1 rounded w-full" />
                </td>
                <td class="border px-4 py-2">{{ cat.slug }}</td>
                <td class="border px-4 py-2">{{ new Date(cat.created_at).toLocaleString() }}</td>
                <td class="border px-4 py-2 flex gap-2">
                    <button @click="updateCategory(cat)" class="px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">Güncelle</button>
                    <button @click="deleteCategory(cat.id)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Sil</button>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Yeni Kategori Ekleme -->
        <div class="flex gap-2">
            <input v-model="newCategory" placeholder="Yeni kategori" class="border p-2 rounded flex-1"/>
            <button @click="addCategory" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Ekle</button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api.js';

const categories = ref([]);
const newCategory = ref('');

const fetchCategories = async () => {
    try {
        const res = await api.get('/categories', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        categories.value = res.data;
    } catch (e) { console.error('Kategoriler yüklenemedi', e); }
};

const addCategory = async () => {
    if (!newCategory.value.trim()) return alert('Kategori adı boş olamaz');
    try {
        const res = await api.post('/categories', { name: newCategory.value }, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        categories.value.push(res.data);
        newCategory.value = '';
    } catch (e) { console.error(e); alert('Kategori eklenemedi'); }
};

const updateCategory = async (cat) => {
    try {
        await api.put(`/categories/${cat.id}`, { name: cat.name }, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        alert('Kategori güncellendi');
    } catch (e) { console.error(e); alert('Kategori güncellenemedi'); }
};

const deleteCategory = async (id) => {
    if (!confirm('Silmek istediğinize emin misiniz?')) return;
    try {
        await api.delete(`/categories/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        categories.value = categories.value.filter(c => c.id !== id);
    } catch (e) { console.error(e); alert('Kategori silinemedi'); }
};

onMounted(fetchCategories);
</script>
