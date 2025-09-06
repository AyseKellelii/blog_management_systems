<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
            <h1 class="text-3xl font-bold text-center mb-6">Kayıt Ol</h1>

            <Form @submit.prevent="submitForm" :validation-schema="schema" class="space-y-4">
                <!-- Ad -->
                <div>
                    <label class="block text-sm font-medium mb-1">Ad</label>
                    <Field name="first_name" v-model="firstName" type="text"
                           class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
                    <ErrorMessage name="first_name" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Soyad -->
                <div>
                    <label class="block text-sm font-medium mb-1">Soyad</label>
                    <Field name="last_name" v-model="lastName" type="text"
                           class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
                    <ErrorMessage name="last_name" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <Field name="email" v-model="email" type="email"
                           class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
                    <ErrorMessage name="email" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Telefon -->
                <div>
                    <label class="block text-sm font-medium mb-1">Telefon</label>
                    <Field name="phone" v-model="phone" type="text"
                           v-mask="'(###) ###-####'"
                           class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
                    <ErrorMessage name="phone" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Şifre -->
                <div>
                    <label class="block text-sm font-medium mb-1">Şifre</label>
                    <Field name="password" v-model="password" type="password"
                           class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
                    <ErrorMessage name="password" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Şifre Tekrar -->
                <div>
                    <label class="block text-sm font-medium mb-1">Şifre Tekrar</label>
                    <Field name="password_confirmation" v-model="passwordConfirmation" type="password"
                           class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"/>
                    <ErrorMessage name="password_confirmation" class="text-red-500 text-sm mt-1"/>
                </div>

                <!-- Kayıt Ol Butonu -->
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200">
                    Kayıt Ol
                </button>

                <p class="text-sm text-center mt-4">
                    Zaten hesabın var mı?
                    <router-link to="/login" class="text-blue-600 hover:underline">Giriş Yap</router-link>
                </p>
            </Form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import {TheMask} from 'vue-the-mask';

const router = useRouter();

const firstName = ref('');
const lastName = ref('');
const email = ref('');
const phone = ref('');
const password = ref('');
const passwordConfirmation = ref('');

// Yup ile form validasyonu
const schema = yup.object({
    first_name: yup.string().required('Ad zorunludur'),
    last_name: yup.string().required('Soyad zorunludur'),
    email: yup.string().email('Geçersiz email').required('Email zorunludur'),
    phone: yup.string().required('Telefon zorunludur'),
    password: yup.string().min(6, 'Şifre en az 6 karakter olmalı').required('Şifre zorunludur'),
    password_confirmation: yup.string()
        .oneOf([yup.ref('password')], 'Şifreler eşleşmeli')
        .required('Şifre tekrar zorunludur'),
});

async function submitForm() {
    try {
        await axios.post('http://127.0.0.1:8000/api/register', {
            first_name: firstName.value,
            last_name: lastName.value,
            email: email.value,
            phone: phone.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
        });

        alert('Kayıt başarılı! Lütfen giriş yapın.');
        router.push('/login');
    } catch (e) {
        alert('Kayıt başarısız!');
        console.error(e.response?.data || e);
    }
}
</script>

<style scoped>
/* Opsiyonel görsel iyileştirmeler için soft gölgeler ve hover efektleri */
</style>
