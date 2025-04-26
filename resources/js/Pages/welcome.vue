<template>
    <div>
        <h1>Troy Web Book Management</h1>
    </div>
    <Link href="/">Go to Welcome Page</Link>
    <Link href="/login">Get log in</Link>
    <Link href="/registration">Sign up</Link>
</template>
<script setup>
    import { Link } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import axios from 'axios';
</script>