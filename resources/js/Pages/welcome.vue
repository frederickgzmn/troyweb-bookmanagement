<template>
    <div>
        <h1>Troy Web Book Management</h1>
    </div>
    <div>
        <h2>Welcome to the Book Management System</h2>
        <p>Manage your books efficiently and effectively.</p>
    </div>

    <Link href="/books">Book dashboard</Link>
    <Link href="/login">Get log in</Link>
    <Link href="/registration">Sign up</Link>
</template>
<script setup>
    import { Link } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
</script>