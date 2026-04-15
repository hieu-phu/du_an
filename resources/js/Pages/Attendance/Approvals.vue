<template>
  <Head title="Duyệt chấm công" />

  <AdminLayout>
    <PageBreadcrumb title="Duyệt chấm công" :items="[{ text: 'Chấm công', link: null }, { text: 'Duyệt công', link: null }]" />

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div v-for="card in summaryCards" :key="card.label" class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm">
        <div class="text-sm text-gray-500">{{ card.label }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Tháng</label>
          <select v-model="filterForm.month" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option v-for="month in filters.months" :key="month.value" :value="month.value">{{ month.label }}</option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Năm</label>
          <select v-model="filterForm.year" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option v-for="year in filters.years" :key="year.value" :value="year.value">{{ year.label }}</option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Nhân viên</label>
          <select v-model="filterForm.employee_profile_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option :value="null">Tất cả</option>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.label }}</option>
          </select>
        </div>
        <div class="flex items-end">
          <button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white" @click="applyFilters">
            Lọc dữ liệu
          </button>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <DataTable :columns="columns" :data="records" :actions="actions" empty-message="Không có bản ghi chờ duyệt.">
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
        <template #cell-late_minutes="{ item }">
          {{ formatMinutes(item.late_minutes) }}
        </template>
        <template #cell-early_leave_minutes="{ item }">
          {{ formatMinutes(item.early_leave_minutes) }}
        </template>
        <template #cell-day_status="{ item }">
          <span :class="dayStatusClass(item.day_status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatDayStatus(item.day_status) }}
          </span>
        </template>
        <template #cell-approval_status="{ item }">
          <span :class="approvalStatusClass(item.approval_status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatApprovalStatus(item.approval_status) }}
          </span>
        </template>
      </DataTable>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  records: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
  employees: { type: Array, default: () => [] },
})

const page = usePage()
const currentRole = computed(() => page.props.auth?.user?.primary_role || 'employee')
const decisionForm = useForm({ note: '' })
const filterForm = reactive({
  month: props.filters.month,
  year: props.filters.year,
  employee_profile_id: props.filters.employee_profile_id,
})

const columns = [
  { label: 'Nhân viên', key: 'employee_name' },
  { label: 'Mã NV', key: 'employee_code' },
  { label: 'Phòng ban', key: 'department_name' },
  { label: 'Ngày công', key: 'work_date' },
  { label: 'Check in', key: 'check_in_at' },
  { label: 'Check out', key: 'check_out_at' },
  { label: 'Giờ làm', key: 'worked_minutes', align: 'text-center' },
  { label: 'Đi muộn', key: 'late_minutes', align: 'text-center' },
  { label: 'Về sớm', key: 'early_leave_minutes', align: 'text-center' },
  { label: 'Trạng thái ngày', key: 'day_status', align: 'text-center' },
  { label: 'Duyệt', key: 'approval_status', align: 'text-center' },
]

const summaryCards = computed(() => [
  { label: 'Chờ duyệt', value: props.summary.pending_records ?? 0 },
  { label: 'Đã duyệt', value: props.summary.confirmed_records ?? 0 },
  { label: 'Từ chối', value: props.summary.rejected_records ?? 0 },
  { label: 'Đi muộn', value: props.summary.late_records ?? 0 },
])

const actions = [
  {
    label: 'Duyệt',
    buttonProps: { title: 'Duyệt ngày công' },
    hidden: (item) => item.approval_status !== 'pending' || (item.employee_role === 'hr' && currentRole.value !== 'admin'),
    onClick: (item) => decide(item, 'approve'),
  },
  {
    label: 'Từ chối',
    buttonProps: { title: 'Từ chối ngày công' },
    hidden: (item) => item.approval_status !== 'pending' || (item.employee_role === 'hr' && currentRole.value !== 'admin'),
    onClick: (item) => decide(item, 'reject'),
  },
]

function applyFilters() {
  router.get(route('attendance.approvals'), {
    month: filterForm.month,
    year: filterForm.year,
    employee_profile_id: filterForm.employee_profile_id,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function decide(item, action) {
  const note = window.prompt(action === 'approve' ? 'Ghi chú duyệt:' : 'Lý do từ chối:', item.approval_note || item.note || '')
  decisionForm.note = note || ''
  decisionForm.post(route(action === 'approve' ? 'attendance.confirm' : 'attendance.reject', item.id), {
    preserveScroll: true,
  })
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

function formatMinutes(value) {
  const minutes = Number(value) || 0
  if (minutes <= 0) return '0 phút'
  const hours = Math.floor(minutes / 60)
  const remainMinutes = minutes % 60
  if (hours <= 0) return `${remainMinutes} phút`
  if (remainMinutes === 0) return `${hours} giờ`
  return `${hours} giờ ${remainMinutes} phút`
}

function formatDayStatus(value) {
  const labels = {
    present: 'Đi làm',
    late: 'Đi muộn',
    early_leave: 'Về sớm',
    leave: 'Nghỉ phép',
    unpaid_leave: 'Nghỉ không phép',
    business_trip: 'Công tác',
    missing_check_in: 'Thiếu check in',
    missing_check_out: 'Thiếu check out',
    absent: 'Vắng',
  }
  return labels[value] || '-'
}

function formatApprovalStatus(value) {
  const labels = {
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    rejected: 'Từ chối',
  }
  return labels[value] || '-'
}

function dayStatusClass(value) {
  const classes = {
    present: 'bg-emerald-50 text-emerald-700',
    late: 'bg-amber-50 text-amber-700',
    early_leave: 'bg-orange-50 text-orange-700',
    leave: 'bg-sky-50 text-sky-700',
    unpaid_leave: 'bg-rose-50 text-rose-700',
    business_trip: 'bg-violet-50 text-violet-700',
    missing_check_in: 'bg-yellow-50 text-yellow-700',
    missing_check_out: 'bg-yellow-50 text-yellow-700',
    absent: 'bg-rose-50 text-rose-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}

function approvalStatusClass(value) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700',
    approved: 'bg-emerald-50 text-emerald-700',
    rejected: 'bg-rose-50 text-rose-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}
</script>
