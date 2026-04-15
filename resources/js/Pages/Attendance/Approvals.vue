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

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bản ghi chấm công chờ duyệt</h3>
      </div>

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

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Đơn chấm công chờ duyệt</h3>
      </div>

      <DataTable :columns="requestColumns" :data="request_approvals" :actions="requestActions" empty-message="Không có đơn chấm công chờ duyệt.">
        <template #cell-request_date="{ item }">
          {{ formatDate(item.request_date) }}
        </template>
        <template #cell-submitted_at="{ item }">
          {{ formatDateTime(item.submitted_at) }}
        </template>
        <template #cell-status_label="{ item }">
          <span :class="approvalStatusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ item.status_label }}
          </span>
        </template>
      </DataTable>
    </div>

    <Modal :show="!!selectedRequest" @close="closeRequestDetail">
      <div v-if="selectedRequest" class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Chi tiết đơn chấm công</h3>
        <div class="grid grid-cols-1 gap-3 text-sm text-gray-700 md:grid-cols-2">
          <div><span class="font-medium text-gray-900">Nhân viên:</span> {{ selectedRequest.employee_name || '-' }}</div>
          <div><span class="font-medium text-gray-900">Mã NV:</span> {{ selectedRequest.employee_code || '-' }}</div>
          <div><span class="font-medium text-gray-900">Phòng ban:</span> {{ selectedRequest.department_name || '-' }}</div>
          <div><span class="font-medium text-gray-900">Loại đơn:</span> {{ selectedRequest.request_type_label || '-' }}</div>
          <div><span class="font-medium text-gray-900">Ngày áp dụng:</span> {{ formatDate(selectedRequest.request_date) }}</div>
          <div><span class="font-medium text-gray-900">Khoảng thời gian:</span> {{ selectedRequest.period || '-' }}</div>
          <div class="md:col-span-2"><span class="font-medium text-gray-900">Lý do:</span> {{ selectedRequest.reason || '-' }}</div>
        </div>

        <div v-if="selectedRequest.target_type === 'attendance'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thông tin bổ sung đơn chấm công</div>
          <div><span class="font-medium text-gray-900">Từ ngày:</span> {{ formatDate(selectedRequest.from_date) }}</div>
          <div><span class="font-medium text-gray-900">Đến ngày:</span> {{ formatDate(selectedRequest.to_date) }}</div>
          <div><span class="font-medium text-gray-900">Từ giờ:</span> {{ selectedRequest.from_time || '-' }}</div>
          <div><span class="font-medium text-gray-900">Đến giờ:</span> {{ selectedRequest.to_time || '-' }}</div>
          <div><span class="font-medium text-gray-900">Loại nghỉ:</span> {{ selectedRequest.leave_type || '-' }}</div>
          <div><span class="font-medium text-gray-900">Trạng thái đề nghị:</span> {{ selectedRequest.requested_status || '-' }}</div>
        </div>

        <div v-if="selectedRequest.target_type === 'overtime'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thông tin tăng ca</div>
          <div><span class="font-medium text-gray-900">Bắt đầu:</span> {{ formatDateTime(selectedRequest.overtime_start_at) }}</div>
          <div><span class="font-medium text-gray-900">Kết thúc:</span> {{ formatDateTime(selectedRequest.overtime_end_at) }}</div>
          <div><span class="font-medium text-gray-900">Phút đề nghị:</span> {{ formatMinutes(selectedRequest.requested_minutes) }}</div>
          <div><span class="font-medium text-gray-900">Phút duyệt hiện tại:</span> {{ formatMinutes(selectedRequest.approved_minutes) }}</div>
        </div>

        <div class="mt-6 flex justify-end">
          <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700" @click="closeRequestDetail">
            Đóng
          </button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'
import Modal from '@/components/ui/Modal.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  records: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
  employees: { type: Array, default: () => [] },
  request_approvals: { type: Array, default: () => [] },
})

const page = usePage()
const currentRole = computed(() => page.props.auth?.user?.primary_role || 'employee')
const selectedRequest = ref(null)
const decisionForm = useForm({ note: '' })
const requestDecisionForm = useForm({ note: '' })
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

const requestColumns = [
  { label: 'Nhân viên', key: 'employee_name' },
  { label: 'Mã NV', key: 'employee_code' },
  { label: 'Loại đơn', key: 'request_type_label' },
  { label: 'Ngày áp dụng', key: 'request_date' },
  { label: 'Khoảng thời gian', key: 'period' },
  { label: 'Lý do', key: 'reason' },
  { label: 'Trạng thái', key: 'status_label', align: 'text-center' },
  { label: 'Gửi lúc', key: 'submitted_at' },
]

const summaryCards = computed(() => [
  { label: 'Công chờ duyệt', value: props.summary.pending_records ?? 0 },
  { label: 'Đã duyệt', value: props.summary.confirmed_records ?? 0 },
  { label: 'Từ chối', value: props.summary.rejected_records ?? 0 },
  { label: 'Đơn chờ duyệt', value: props.request_approvals.length },
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

const requestActions = [
  {
    label: 'Chi tiết',
    buttonProps: { title: 'Xem chi tiết đơn' },
    onClick: (item) => openRequestDetail(item),
  },
  {
    label: 'Duyệt đơn',
    buttonProps: { title: 'Duyệt đơn chấm công' },
    hidden: (item) => item.status !== 'pending',
    onClick: (item) => reviewRequest(item, 'approve'),
  },
  {
    label: 'Từ chối',
    buttonProps: { title: 'Từ chối đơn chấm công' },
    hidden: (item) => item.status !== 'pending',
    onClick: (item) => reviewRequest(item, 'reject'),
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
  if (note === null) return
  if (String(note).trim().length < 5) {
    window.alert('Ghi chú tối thiểu 5 ký tự.')
    return
  }
  decisionForm.note = String(note).trim()
  decisionForm.post(route(action === 'approve' ? 'attendance.confirm' : 'attendance.reject', item.id), {
    preserveScroll: true,
  })
}

function reviewRequest(item, action) {
  const note = window.prompt(action === 'approve' ? 'Ghi chú duyệt đơn:' : 'Lý do từ chối đơn:', item.reason || '')
  if (note === null) return
  if (String(note).trim().length < 5) {
    window.alert('Ghi chú tối thiểu 5 ký tự.')
    return
  }
  requestDecisionForm.note = String(note).trim()
  requestDecisionForm.post(route(action === 'approve' ? 'attendance.request-approvals.approve' : 'attendance.request-approvals.reject', item.id), {
    preserveScroll: true,
  })
}

function openRequestDetail(item) {
  selectedRequest.value = item
}

function closeRequestDetail() {
  selectedRequest.value = null
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

