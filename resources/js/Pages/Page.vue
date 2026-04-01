<template>
    <AdminLayout>

        <Head title="Dashboard - Ecommerce"></Head>
        <PageBreadcrumb title="Document" :items="[{ title: 'Dashboard', link: '' }, 'Page']" />
        <div class="my-4">
            Nội dung chung
        </div>
        <Pagination />
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Pagination from '@/components/Pagination.vue';
import { LayoutDashboardIcon, UserGroupIcon, BoxIcon, BarChartIcon } from '@/icons'

</script>