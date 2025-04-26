<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';

    const name = ref('');
    const email = ref('');
    const password = ref('');
    const password_confirmation = ref('');
    const errorMessages = ref(null);
    const isLoading = ref(false);

    const register_method = async () => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            const response = await axios.post('/api/v1/auth/register', {
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation: password_confirmation.value,
            });

            const { access_token, token_type } = response.data;
            localStorage.setItem('access_token', access_token);
            localStorage.setItem('token_type', token_type);

            // redirect to dashboard.
            router.visit('/login');
            
        } catch (error) {
            if (error.response && error.response.status === 422) {
                errorMessages.value = error.response.data.errors;
            } else {
                console.error('Registration failed:', error);
            }
        } finally {
            isLoading.value = false;
        }
    };
</script>

<template>  
    <div>
        <h1>Registration</h1>
        <form @submit.prevent="register_method">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" v-model="name" required />
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" v-model="email" required />
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" v-model="password" required />
            </div>
            <div>
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" v-model="password_confirmation" required />
            </div>
            <button type="submit" :disabled="isLoading">
                Register
            </button>
        </form>

        <!-- Display error messages -->
        <div v-if="errorMessages">
            <ul>
                <li v-for="(errors, field) in errorMessages" :key="field">{{ field }}: {{ errors[0] }}</li>
            </ul>
        </div>

        <!-- Link to login page -->
        <Link href="/login">Already have an account? Login here</Link>
    </div>
</template>