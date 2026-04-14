<template>
  <Head title="Cong cua toi" />

  <AdminLayout>
    <PageBreadcrumb title="Cong cua toi" :items="[{ text: 'Cham cong', link: null }, { text: 'Cong cua toi', link: null }]" />

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <DataTable
        :columns="columns"
        :data="records"
        empty-message="Chua co du lieu cham cong."
      >
        <template #cell-work_date="{ item }">
          {{ formatDate(item.work_date) }}
        </template>

        <template #cell-check_in_at="{ item }">
          {{ formatDateTime(item.check_in_at) }}
        </template>

        <template #cell-check_out_at="{ item }">
          {{ formatDateTime(item.check_out_at) }}
        </template>

        <template #cell-worked_minutes="{ item }">
          {{ formatMinutes(item.worked_minutes) }}
        </template>

        <template #cell-attendance_status="{ item }">
          <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
            {{ item.attendance_status || '-' }}
          </span>
        </template>

        <template #cell-is_confirmed="{ item }">
          <span
            :class="item.is_confirmed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
            class="rounded-full px-3 py-1 text-xs font-semibold"
          >
            {{ item.is_confirmed ? 'Da duyet' : 'Cho duyet' }}
          </span>
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
  records: { type: Array, default: () => [] },
})

const columns = [
  { label: 'Ngay cong', key: 'work_date' },
  { label: 'Check in', key: 'check_in_at' },
  { label: 'Check out', key: 'check_out_at' },
  { label: 'So phut lam', key: 'worked_minutes', align: 'text-center' },
  { label: 'Trang thai', key: 'attendance_status', align: 'text-center' },
  { label: 'Duyet', key: 'is_confirmed', align: 'text-center' },
]

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

function formatMinutes(value) {
  if (value === null || value === undefined) return '-'
  return `${value} phut`
}
</script>
