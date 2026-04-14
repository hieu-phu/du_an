<template>
  <Head title="Cấu hình website" />

  <AdminLayout>
    <PageBreadcrumb title="Cấu hình website" :items="[{ text: 'Cấu hình', link: null }, { text: 'Website', link: null }]" />

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <h3 class="text-lg font-semibold text-gray-900">Cấu hình website</h3>
      <p class="mt-2 text-sm text-gray-500">
        Khu vực cấu hình hệ thống dành cho Admin. Module này đã có sẵn layout và menu, có thể mở rộng tiếp cho các cấu hình sau.
      </p>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
</script>
