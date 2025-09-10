<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
            <h1 class="text-3xl font-bold text-center mb-6">Giriş Yap</h1>
            <Form @submit="submitForm" :validation-schema="schema" class="space-y-4">

                <!-- Email veya Telefon -->
                <div>
                    <label class="block text-sm font-medium mb-1">Email veya Telefon</label>
                    <Field
                        name="login"
                        v-model="login"
                        type="text"
                        autocomplete="username"
                        class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                    />
                    <ErrorMessage name="login" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Şifre -->
                <div>
                    <label class="block text-sm font-medium mb-1">Şifre</label>
                    <Field
                        name="password"
                        v-model="password"
                        type="password"
                        autocomplete="current-password"
                        class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-500 focus:outline-none"
                    />
                    <ErrorMessage name="password" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Giriş Butonu -->
                <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-semibold transition duration-200">
                    Giriş Yap
                </button>

                <p class="text-sm text-center mt-4">
                    Henüz hesabın yok mu?
                    <router-link to="/register" class="text-blue-600 hover:underline">Kayıt Ol</router-link>
                </p>
            </Form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import api from '../../api.js';

const router = useRouter();
const login = ref('');
const password = ref('');

// Yup ile form validasyonu
const schema = yup.object({
    login: yup.string().required('Email veya telefon zorunludur'),
    password: yup.string().required('Şifre zorunludur'),
});

async function submitForm() {
    try {
        const res = await api.post('/login', {
            login: login.value,
            password: password.value,
        });

        // Token ve kullanıcı bilgilerini sakla
        localStorage.setItem('api_token', res.data.token);
        localStorage.setItem('current_user', JSON.stringify(res.data.user));

        // Global olarak current user
        window.__CURRENT_USER__ = res.data.user;

        // Redirect backend’den gelen URL ile
        router.push(res.data.redirect_url);

    } catch (e) {
        alert('Giriş başarısız!');
        console.error(e.response?.data || e);
    }
}
</script>


<style>
/* Opsiyonel ekstra CSS */
</style>
