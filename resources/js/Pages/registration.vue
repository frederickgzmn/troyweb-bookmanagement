<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
    import Login from './login.vue';

    const name = ref('');
    const email = ref('');
    const password = ref('');
    const role = ref('');
    const password_confirmation = ref('');
    const errorMessages = ref(null);
    const notificationSuccess = ref(null);
    const isLoading = ref(false);

    // lIst of roles from the api server
    defineProps({
        roles: Array,
    });

    // registration request to the api server
    const register_method = async () => {
        isLoading.value = true;
        errorMessages.value = null;

        try {
            // Validate input fields
            if ( !name.value || !email.value || !password.value || !password_confirmation.value ) {
                errorMessages.value = { general: ['All fields are required'] };
                isLoading.value = false;
                return;
            }

            // The passwords aren't matching
            if ( password.value !== password_confirmation.value ) {
                errorMessages.value = { password: 'Passwords do not match' };
                isLoading.value = false;
                return;
            }

            // Sending the ajax request to the api server
            const response = await axios.post('/api/v1/auth/register', {
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation : password_confirmation.value,
                role_id: role.value,
            });

            // capture the errors.
            if ( response.data.error ) {
                errorMessages.value = response.data.error;
            }

            // If a name is assigned the registration was successful.
            if ( response.data.name ) {
                console.log('Registration successful:', response.data.name);
                notificationSuccess.value = response.data.name + ' registered successfully.';
                isLoading.value = false;

                // Redirect to login page after 2 secnds.
                setTimeout(() => {
                    router.visit('/login');
                }, 2000 );
            }

        } catch (error) {
            console.error('Registration failed:', error);
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
            <div>
                <label for="role">Role:</label>
                <select id="role" v-model="role" required>
                    <option value="" selected="true" >Select a role</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                </select>
            </div>
            <button type="submit" :disabled="isLoading">
                Register
            </button>
        </form>

        <!-- Display error messages -->
        <div v-if="errorMessages" class="error-messages">
            <p v-for="(message, index) in errorMessages" :key="index">{{ message }}</p>
        </div>

        <!-- Display success message -->
        <div v-if="notificationSuccess" class="success-message">
            <p>{{ notificationSuccess }}</p>
        </div>

        <!-- Link to login page -->
        <Link href="/login">Already have an account? Login here</Link>
    </div>
</template>