<template>
  <Head :title="title" />

  <AdminLayout>
    <PageBreadcrumb :title="title" :items="[{ text: 'Du an', link: null }, { text: title, link: null }]" />

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4 flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-gray-900">{{ title }}</h2>
          <p class="text-sm text-gray-500">
            {{ scope === 'mine' ? 'Chi hien thi du an lien quan den tai khoan cua ban.' : 'Danh sach du an toan he thong.' }}
          </p>
        </div>
      </div>

      <DataTable :columns="columns" :data="projects" empty-message="Chua co du an nao.">
        <template #cell-status="{ item }">
          <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
            {{ item.status || '-' }}
          </span>
        </template>

        <template #cell-start_date="{ item }">
          {{ formatDate(item.start_date) }}
        </template>
      </DataTable>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'

defineProps({
  projects: { type: Array, default: () => [] },
  scope: { type: String, default: 'all' },
  title: { type: String, default: 'Danh sach du an' },
})

const columns = [
  { label: 'Ten du an', key: 'name' },
  { label: 'Trang thai', key: 'status', align: 'text-center' },
  { label: 'Ngay bat dau', key: 'start_date', align: 'text-center' },
  { label: 'Mo ta', key: 'description' },
]

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}
</script>
