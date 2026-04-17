<template>
  <Head title="Công của tôi" />

  <AdminLayout>
    <PageBreadcrumb title="Công của tôi" :items="[{ text: 'Chấm công', link: null }, { text: 'Công của tôi', link: null }]" />

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div v-for="card in summaryCards" :key="card.label" class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm">
        <div class="text-sm text-gray-500">{{ card.label }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
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
        <div class="flex items-end">
          <button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white" @click="applyFilters">
            Xem dữ liệu
          </button>
        </div>
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Gửi đơn liên quan chấm công</h3>
        <p class="mt-1 text-sm text-gray-500">Dùng cho nghỉ phép, quên chấm công, công tác, làm bù hoặc tăng ca.</p>
      </div>

      <div v-if="formError" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ formError }}
      </div>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Loại đơn</label>
          <select v-model="requestForm.request_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Chọn loại đơn</option>
            <option v-for="type in request_types" :key="type.value" :value="type.value">{{ requestTypeLabel(type.value) }}</option>
          </select>
          <p v-if="requestForm.errors.request_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.request_type }}</p>
        </div>
        <div v-if="requestForm.request_type !== 'overtime'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Ngày áp dụng</label>
          <input v-model="requestForm.request_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2">
          <p v-if="requestForm.errors.request_date" class="mt-1 text-sm text-red-500">{{ requestForm.errors.request_date }}</p>
        </div>
        <div v-if="requestForm.request_type === 'leave'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Loại nghỉ</label>
          <select v-model="requestForm.leave_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="paid">Nghỉ phép</option>
            <option value="unpaid">Nghỉ không phép</option>
          </select>
          <p v-if="requestForm.errors.leave_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_type }}</p>
        </div>
        <div v-if="requestForm.request_type === 'late_early'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Trạng thái đề nghị</label>
          <select v-model="requestForm.requested_status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="late">Xin đi muộn</option>
            <option value="early_leave">Xin về sớm</option>
          </select>
          <p v-if="requestForm.errors.requested_status" class="mt-1 text-sm text-red-500">{{ requestForm.errors.requested_status }}</p>
        </div>
        <div v-if="requestForm.request_type && requestForm.request_type !== 'overtime'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Từ ngày</label>
          <input v-model="requestForm.from_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2">
          <p v-if="requestForm.errors.from_date" class="mt-1 text-sm text-red-500">{{ requestForm.errors.from_date }}</p>
        </div>
        <div v-if="requestForm.request_type && requestForm.request_type !== 'overtime'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Đến ngày</label>
          <input v-model="requestForm.to_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2">
          <p v-if="requestForm.errors.to_date" class="mt-1 text-sm text-red-500">{{ requestForm.errors.to_date }}</p>
        </div>
        <div v-if="requestForm.request_type && requestForm.request_type !== 'overtime'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Từ giờ</label>
          <input v-model="requestForm.from_time" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2">
          <p v-if="requestForm.errors.from_time" class="mt-1 text-sm text-red-500">{{ requestForm.errors.from_time }}</p>
        </div>
        <div v-if="requestForm.request_type && requestForm.request_type !== 'overtime'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Đến giờ</label>
          <input v-model="requestForm.to_time" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2">
          <p v-if="requestForm.errors.to_time" class="mt-1 text-sm text-red-500">{{ requestForm.errors.to_time }}</p>
        </div>
        <div v-if="requestForm.request_type === 'overtime'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Bắt đầu tăng ca</label>
          <input v-model="requestForm.start_at" type="datetime-local" class="w-full rounded-lg border border-gray-300 px-3 py-2">
          <p v-if="requestForm.errors.start_at" class="mt-1 text-sm text-red-500">{{ requestForm.errors.start_at }}</p>
        </div>
        <div v-if="requestForm.request_type === 'overtime'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Kết thúc tăng ca</label>
          <input v-model="requestForm.end_at" type="datetime-local" class="w-full rounded-lg border border-gray-300 px-3 py-2">
          <p v-if="requestForm.errors.end_at" class="mt-1 text-sm text-red-500">{{ requestForm.errors.end_at }}</p>
        </div>
      </div>

      <div class="mt-4">
        <label class="mb-2 block text-sm font-medium text-gray-700">Lý do</label>
        <textarea v-model="requestForm.reason" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Nhập lý do chi tiết"></textarea>
        <p v-if="requestForm.errors.reason" class="mt-1 text-sm text-red-500">{{ requestForm.errors.reason }}</p>
      </div>

      <div class="mt-4 flex justify-end">
        <button
          type="button"
          class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
          :disabled="requestForm.processing"
          @click="submitAttendanceRequest"
        >
          {{ requestForm.processing ? 'Đang gửi...' : 'Gửi đơn' }}
        </button>
      </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bảng công cá nhân</h3>
      </div>

      <DataTable :columns="columns" :data="records" empty-message="Chưa có dữ liệu chấm công.">
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
        <template #cell-overtime_minutes="{ item }">
          {{ formatMinutes(item.overtime_minutes) }}
        </template>
        <template #cell-attendance_status="{ item }">
          <span :class="statusClass(item.attendance_status, item.day_status, item.work_unit)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatAttendanceStatus(item.attendance_status, item.day_status, item.work_unit) }}
          </span>
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

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Đơn chấm công đã gửi</h3>
        <p class="mt-1 text-sm text-gray-500">Theo dõi các đơn bạn đã gửi và trạng thái duyệt hiện tại.</p>
      </div>

      <DataTable :columns="requestColumns" :data="recent_requests" empty-message="Bạn chưa gửi đơn chấm công nào.">
        <template #cell-request_type_label="{ item }">
          {{ requestTypeLabel(item.request_type) }}
        </template>
        <template #cell-request_date="{ item }">
          {{ formatDate(item.request_date) }}
        </template>
        <template #cell-status_label="{ item }">
          <span :class="approvalStatusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ item.status_label }}
          </span>
        </template>
        <template #cell-submitted_at="{ item }">
          {{ formatDateTime(item.submitted_at) }}
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
  request_types: { type: Array, default: () => [] },
  recent_requests: { type: Array, default: () => [] },
})

const page = usePage()

const filterForm = reactive({
  month: props.filters.month,
  year: props.filters.year,
})

const requestForm = useForm({
  request_type: '',
  request_date: '',
  from_date: '',
  to_date: '',
  from_time: '',
  to_time: '',
  leave_type: 'paid',
  requested_status: 'late',
  start_at: '',
  end_at: '',
  reason: '',
})

const columns = [
  { label: 'Ngày công', key: 'work_date' },
  { label: 'Check in', key: 'check_in_at' },
  { label: 'Check out', key: 'check_out_at' },
  { label: 'Giờ làm', key: 'worked_minutes', align: 'text-center' },
  { label: 'Đi muộn', key: 'late_minutes', align: 'text-center' },
  { label: 'Về sớm', key: 'early_leave_minutes', align: 'text-center' },
  { label: 'Tăng ca', key: 'overtime_minutes', align: 'text-center' },
  { label: 'Trạng thái công', key: 'attendance_status', align: 'text-center' },
  { label: 'Trạng thái ngày', key: 'day_status', align: 'text-center' },
  { label: 'Duyệt', key: 'approval_status', align: 'text-center' },
  { label: 'Công thức áp dụng', key: 'formula_detail' },
]

const requestColumns = [
  { label: 'Loại đơn', key: 'request_type_label' },
  { label: 'Ngày áp dụng', key: 'request_date' },
  { label: 'Khoảng thời gian', key: 'period' },
  { label: 'Trạng thái', key: 'status_label', align: 'text-center' },
  { label: 'Người duyệt', key: 'reviewed_by_name' },
  { label: 'Ghi chú duyệt', key: 'review_note' },
  { label: 'Gửi lúc', key: 'submitted_at' },
]

const summaryCards = computed(() => [
  { label: 'Tổng ngày công', value: formatWorkUnits(props.summary.total_work_units ?? 0) },
  { label: 'Đã duyệt', value: props.summary.confirmed_records ?? 0 },
  { label: 'Chờ duyệt', value: props.summary.pending_records ?? 0 },
  { label: 'Tổng giờ làm', value: formatMinutes(props.summary.total_worked_minutes ?? 0) },
])

const formError = computed(() => requestForm.errors.error || page.props.errors?.error || '')

const REQUEST_TYPE_LABELS = {
 leave: 'Xin nghỉ phép',
late_early: 'Xin đi muộn / về sớm',
forgot_check: 'Xin quên chấm công',
business_trip: 'Xin công tác',
make_up: 'Xin làm bù',
overtime: 'Đăng ký tăng ca',
}

function requestTypeLabel(value) {
  return REQUEST_TYPE_LABELS[value] || value || '-'
}

function applyFilters() {
  router.get(route('attendance.mine'), {
    month: filterForm.month,
    year: filterForm.year,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function submitAttendanceRequest() {
  requestForm.post(route('attendance.requests.store'), {
    preserveScroll: true,
    onSuccess: () => requestForm.reset(),
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

function formatWorkUnits(value) {
  const units = Number(value) || 0
  return Number.isInteger(units) ? `${units}` : units.toFixed(1)
}

function formatAttendanceStatus(value, dayStatus = null, workUnit = null) {
  const resolvedUnit = Number(workUnit)

  if (resolvedUnit === 1) {
    return 'Đủ công'
  }

  if (resolvedUnit === 0.5) {
    return 'Nửa công'
  }

  if (!Number.isNaN(resolvedUnit) && resolvedUnit === 0) {
    return 'Không công'
  }

  if (dayStatus === 'early_leave' || dayStatus === 'missing_check_in' || dayStatus === 'missing_check_out') {
    return 'Không công'
  }

  const labels = {
    on_time: 'Đúng giờ',
    late: 'Trễ',
    absent: 'Vắng',
  }
  return labels[value] || '-'
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

function statusClass(value, dayStatus = null, workUnit = null) {
  const resolvedUnit = Number(workUnit)

  if (resolvedUnit === 1) {
    return 'bg-emerald-50 text-emerald-700'
  }

  if (resolvedUnit === 0.5) {
    return 'bg-blue-50 text-blue-700'
  }

  if (!Number.isNaN(resolvedUnit) && resolvedUnit === 0) {
    return 'bg-orange-50 text-orange-700'
  }

  if (dayStatus === 'early_leave' || dayStatus === 'missing_check_in' || dayStatus === 'missing_check_out') {
    return 'bg-orange-50 text-orange-700'
  }

  return value === 'late'
    ? 'bg-amber-50 text-amber-700'
    : value === 'absent'
      ? 'bg-rose-50 text-rose-700'
      : 'bg-emerald-50 text-emerald-700'
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



