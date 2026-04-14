<template>
  <Head title="Báo cáo chấm công" />
  <AdminLayout>
    <PageBreadcrumb title="Báo cáo chấm công" :items="[{ text: 'Chấm công', link: null }, { text: 'Báo cáo', link: null }]" />
    
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
      <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-100">Báo cáo tháng</h3>
      <p class="text-sm text-gray-500">Màn hình đang trong quá trình xây dựng...</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
</script>
