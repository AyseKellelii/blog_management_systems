<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const categories = ref([]);
const loading = ref(false);
const error = ref(null);
const newCategoryName = ref('');
const editCategoryId = ref(null);
const editCategoryName = ref('');

const fetchCategories = async () => {
    loading.value = true;
    error.value = null;
    try {
        const res = await axios.get('http://127.0.0.1:8000/api/categories', {
            headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` }
        });
        categories.value = res.data;
    } catch (e) {
        error.value = "Kategoriler yüklenemedi.";
    } finally {
        loading.value = false;
    }
};

const createCategory = async () => {
    if (!newCategoryName.value) return;
    try {
        await axios.post('http://127.0.0.1:8000/api/categories',
            { name: newCategoryName.value },
            { headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` } }
        );
        newCategoryName.value = '';
        fetchCategories();
    } catch (e) {
        alert("Kategori eklenemedi.");
    }
};

const startEdit = (category) => {
    editCategoryId.value = category.id;
    editCategoryName.value = category.name;
};

const updateCategory = async () => {
    if (!editCategoryName.value || !editCategoryId.value) return;
    try {
        await axios.put(`http://127.0.0.1:8000/api/categories/${editCategoryId.value}`,
            { name: editCategoryName.value },
            { headers: { Authorization: `Bearer ${localStorage.getItem('api_token')}` } }
        );
        editCategoryId.value = null;
        editCategoryName.value = '';
        fetchCategories();
    } catch (e) {
        alert("Kategori güncellenemedi.");
    }
};

const deleteCategory = async (id) => {
    if (!confirm("Bu kategoriyi silmek istediğinize emin misiniz?")) return;
    try {
        await axios.delete(`http://127.0.0.1:8000/api/categories/${id}`, {
            headers: {Authorization: `Bearer ${localStorage.getItem('api_token')}`}
        });
        fetchCategories();
    } catch (e) {
        alert("Kategori silinemedi.");
    }
};

onMounted(fetchCategories);
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Kategori Yönetimi</h1>

        <div v-if="loading">Yükleniyor...</div>
        <div v-if="error" class="text-red-600">{{ error }}</div>

        <!-- Yeni Kategori -->
        <div class="mb-4 flex gap-2">
            <input v-model="newCategoryName" type="text" placeholder="Yeni kategori adı" class="border p-2 rounded">
            <button @click="createCategory" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Ekle
            </button>
        </div>

        <!-- Kategori Listesi -->
        <table class="min-w-full border border-gray-200">
            <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Kategori Adı</th>
                <th class="p-2 border">İşlemler</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="category in categories" :key="category.id">
                <td class="p-2 border">{{ category.id }}</td>
                <td class="p-2 border">
                    <span v-if="editCategoryId !== category.id">{{ category.name }}</span>
                    <input v-else v-model="editCategoryName" class="border p-1 rounded w-full">
                </td>
                <td class="p-2 border flex gap-2">
                    <button v-if="editCategoryId !== category.id" @click="startEdit(category)"
                            class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Düzenle
                    </button>
                    <button v-else @click="updateCategory"
                            class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Kaydet
                    </button>
                    <button @click="deleteCategory(category.id)"
                            class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Sil
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
