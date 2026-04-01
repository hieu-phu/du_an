<script setup>
defineProps({ title: String });
</script>

<template>

    <Head title="Trang tài liệu"></Head>
    <div class="mt-5 mb-5">
        <h1 class="text-center">TÀI LIỆU GIAO DIỆN CHUNG</h1>
    </div>

    <Pagination
    
    />
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3';
import Pagination from '../Components/Pagination.vue';

export default {
    layout: AdminLayout,
}
</script>

<style>
.asfy__header {
    display: block !important;
    width: 100% !important;
}
</style>