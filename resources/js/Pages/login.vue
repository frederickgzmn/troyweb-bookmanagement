<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
    
    // Variables to use.
    const email = ref('');
    const password = ref('');
    const errorMessages = ref(null);
    const isLoading = ref(false);

    axios.interceptors.request.use((config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    });

    // Login method.
    const login_method = async () => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Send login request to the api.
            const response = await axios.post('/api/v1/auth/login', {
                email: email.value,
                password: password.value,
            });

            // Check if the login was successful.
            if ( response.data.token ) {
                // Storing the access token and token type.
                localStorage.setItem( 'user', JSON.stringify( response.data.user ) );
                localStorage.setItem( 'token', response.data.token );

                // redirect to dashboard.
                router.visit('/books');
            } else {
                errorMessages.value = error;
            }
            
        } catch (error) {
            // Handle error response.
            console.error( 'TWBM Internal error:', error );
        } finally {
            // Reset loading state.
            isLoading.value = false;
        }
    };

</script>

<template>
    <div>
        <h1>Login</h1>
        <form @submit.prevent="login_method">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" v-model="email" required />
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" v-model="password" required />
            </div>
            <button type="submit" :disabled="isLoading">
                Login
            </button>
            <p v-if="errorMessages">{{ errorMessages }}</p>
        </form>

        <Link href="/">Go to Welcome Page</Link>
    </div>
</template>