<template>
  <Head title="Báo cáo chấm công" />

  <AdminLayout>
    <PageBreadcrumb title="Báo cáo chấm công" :items="[{ text: 'Chấm công', link: null }, { text: 'Báo cáo', link: null }]" />

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div v-for="card in summaryCards" :key="card.label" class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm">
        <div class="text-sm text-gray-500">{{ card.label }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
        <div v-if="card.hint" class="mt-1 text-xs text-gray-500">{{ card.hint }}</div>
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
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
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Từ khóa</label>
          <input v-model="filterForm.keyword" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Tên, mã NV, trạng thái" />
        </div>
        <div class="flex items-end">
          <button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white" @click="applyFilters">
            Xem báo cáo
          </button>
        </div>
      </div>

      <div class="mt-4 flex flex-wrap items-center gap-3">
        <a :href="exportExcelUrl" class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
          Xuất Excel
        </a>
        <a :href="exportPdfUrl" class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">
          Xuất PDF
        </a>
        <span :class="month_lock?.is_locked ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700'" class="rounded-full px-3 py-2 text-xs font-semibold">
          {{ month_lock?.is_locked ? 'Bảng công đang khóa' : 'Bảng công đang mở' }}
        </span>
        <button
          v-if="canManageMonthLock && !month_lock?.is_locked"
          type="button"
          class="rounded-lg border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700"
          @click="toggleMonthLock('lock')"
        >
          Khóa tháng
        </button>
        <button
          v-if="canManageMonthLock && month_lock?.is_locked"
          type="button"
          class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700"
          @click="toggleMonthLock('unlock')"
        >
          Mở khóa tháng
        </button>
      </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <DataTable
        :columns="columns"
        :data="records"
        :row-class="attendanceRowClass"
        paginate
        :default-per-page="10"
        empty-message="Không có dữ liệu báo cáo."
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
        <template #cell-late_minutes="{ item }">
          {{ formatMinutes(item.late_minutes) }}
        </template>
        <template #cell-early_leave_minutes="{ item }">
          {{ formatMinutes(item.early_leave_minutes) }}
        </template>
        <template #cell-overtime_minutes="{ item }">
          {{ formatMinutes(item.overtime_minutes) }}
        </template>
        <template #cell-day_status="{ item }">
          <span :class="dayStatusClass(item.day_status)" class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatDayStatus(item) }}
          </span>
        </template>
        <template #cell-violation_status="{ item }">
          <span :class="violationStatusClass(item.violation_status)" class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatViolationStatus(item.violation_status) }}
          </span>
        </template>
        <template #cell-approval_status="{ item }">
          <span :class="approvalStatusClass(item.display_approval_status || item.approval_status)" class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatApprovalStatus(item.display_approval_status || item.approval_status) }}
          </span>
        </template>
      </DataTable>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Chi tiết tăng ca</h3>
      </div>

      <DataTable
        :columns="overtimeColumns"
        :data="overtime_details"
        :row-class="requestRowClass"
        paginate
        :default-per-page="10"
        empty-message="Không có dữ liệu tăng ca trong thời gian đã chọn."
      >
        <template #cell-work_date="{ item }">
          {{ formatDate(item.work_date) }}
        </template>
        <template #cell-start_at="{ item }">
          {{ formatDateTime(item.start_at) }}
        </template>
        <template #cell-end_at="{ item }">
          {{ formatDateTime(item.end_at) }}
        </template>
        <template #cell-requested_minutes="{ item }">
          {{ formatMinutes(item.requested_minutes) }}
        </template>
        <template #cell-approved_minutes="{ item }">
          {{ formatMinutes(item.approved_minutes) }}
        </template>
        <template #cell-status="{ item }">
          <span :class="approvalStatusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatApprovalStatus(item.status) }}
          </span>
        </template>
      </DataTable>
    </div>

    <CustomModal
      v-if="monthLockConfirm.open"
      title="Xác nhận thao tác bảng công"
      :custom_class="monthLockModalClasses"
      @close="closeMonthLockConfirm"
    >
      <template #body>
        <div class="space-y-5 px-6 pb-6">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Xem trước xác nhận</p>
                <h4 class="mt-2 text-xl font-semibold text-slate-900">
                  {{ monthLockConfirm.action === 'lock' ? 'Khóa bảng công theo tháng' : 'Mở khóa bảng công theo tháng' }}
                </h4>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                  Modal này mới dựng phần giao diện confirm. Chưa gắn vào chức năng xử lý thật.
                </p>
              </div>
              <span
                :class="monthLockConfirm.action === 'lock' ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-amber-200 bg-amber-50 text-amber-700'"
                class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold"
              >
                {{ monthLockConfirm.action === 'lock' ? 'Chuẩn bị khóa' : 'Chuẩn bị mở khóa' }}
              </span>
            </div>
          </div>

          <div class="grid gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
              <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Kỳ công</div>
              <div class="mt-2 text-lg font-semibold text-gray-900">{{ monthLockPeriodLabel }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
              <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Trạng thái hiện tại</div>
              <div class="mt-2">
                <span
                  :class="month_lock?.is_locked ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700'"
                  class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                >
                  {{ month_lock?.is_locked ? 'Đang khóa' : 'Đang mở' }}
                </span>
              </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
              <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Trạng thái sau thao tác</div>
              <div class="mt-2">
                <span
                  :class="monthLockConfirm.action === 'lock' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700'"
                  class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                >
                  {{ monthLockConfirm.action === 'lock' ? 'Sẽ chuyển sang khóa' : 'Sẽ chuyển sang mở khóa' }}
                </span>
              </div>
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">
              {{ monthLockConfirm.action === 'lock' ? 'Ghi chú khóa tháng' : 'Ghi chú mở khóa' }}
            </label>
            <textarea
              v-model="monthLockConfirm.note"
              rows="4"
              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              :placeholder="monthLockConfirm.action === 'lock' ? 'Ví dụ: khóa kỳ công sau khi HR đã rà soát xong.' : 'Ví dụ: mở khóa để điều chỉnh lại công của nhân sự.'"
            />
            <p class="mt-2 text-xs text-gray-500">Ghi chú sẽ được gửi cùng thao tác khóa hoặc mở khóa bảng công.</p>
            <p v-if="monthLockForm.errors.note" class="mt-2 text-xs font-medium text-rose-600">{{ monthLockForm.errors.note }}</p>
          </div>

          <div class="rounded-xl border border-dashed border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
            Xác nhận sẽ gọi trực tiếp tới chức năng khóa hoặc mở khóa tháng của hệ thống.
          </div>

          <div class="flex flex-wrap justify-end gap-3">
            <button
              type="button"
              :disabled="monthLockForm.processing"
              class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
              @click="closeMonthLockConfirm"
            >
              Đóng
            </button>
            <button
              type="button"
              :disabled="monthLockForm.processing"
              :class="monthLockConfirm.action === 'lock'
                ? 'rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:bg-rose-300'
                : 'rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:bg-amber-300'"
              @click="confirmMonthLockPreview"
            >
              {{
                monthLockForm.processing
                  ? 'Đang xử lý...'
                  : monthLockConfirm.action === 'lock'
                    ? 'Xác nhận khóa tháng'
                    : 'Xác nhận mở khóa'
              }}
            </button>
          </div>
        </div>
      </template>
    </CustomModal>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'
import CustomModal from '@/components/modals/CustomModal.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  records: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
  overtime_details: { type: Array, default: () => [] },
  employees: { type: Array, default: () => [] },
  can_view_all: { type: Boolean, default: false },
  month_lock: { type: Object, default: () => ({ is_locked: false }) },
})

const page = usePage()
const canManageMonthLock = computed(() => page.props.auth?.position_capabilities?.approve_attendance === true)
const filterForm = reactive({
  month: props.filters.month,
  year: props.filters.year,
  employee_profile_id: props.filters.employee_profile_id,
  keyword: props.filters.keyword || '',
})
const monthLockConfirm = reactive({
  open: false,
  action: 'lock',
  note: '',
})
const monthLockForm = useForm({
  month: props.filters.month,
  year: props.filters.year,
  note: '',
})
const monthLockModalClasses = [
  'relative',
  'w-full',
  'max-w-[720px]',
  'flex',
  'flex-col',
  'rounded-2xl',
  'bg-white',
  'overflow-hidden',
  'max-h-[90vh]',
]

const columns = computed(() => {
  const baseColumns = [
    { label: 'Ngày công', key: 'work_date' },
    { label: 'Check in', key: 'check_in_at' },
    { label: 'Check out', key: 'check_out_at' },
    { label: 'Giờ làm', key: 'worked_minutes', align: 'text-center' },
    { label: 'Đi muộn', key: 'late_minutes', align: 'text-center' },
    { label: 'Về sớm', key: 'early_leave_minutes', align: 'text-center' },
    { label: 'Tăng ca', key: 'overtime_minutes', align: 'text-center' },
    { label: 'Trạng thái ngày', key: 'day_status', align: 'text-center', class: 'min-w-[132px] whitespace-nowrap' },
    { label: 'Vi phạm', key: 'violation_status', align: 'text-center', class: 'min-w-[132px] whitespace-nowrap' },
    { label: 'Duyệt', key: 'approval_status', align: 'text-center', class: 'min-w-[118px] whitespace-nowrap' },
  ]

  if (!props.can_view_all) {
    return baseColumns
  }

  return [
    { label: 'Nhân viên', key: 'employee_name' },
    { label: 'Mã NV', key: 'employee_code' },
    { label: 'Phòng ban', key: 'department_name' },
    ...baseColumns,
  ]
})

const overtimeColumns = computed(() => {
  const baseColumns = [
    { label: 'Ngày tăng ca', key: 'work_date' },
    { label: 'Bắt đầu', key: 'start_at' },
    { label: 'Kết thúc', key: 'end_at' },
    { label: 'Phút đề nghị', key: 'requested_minutes', align: 'text-center' },
    { label: 'Phút duyệt', key: 'approved_minutes', align: 'text-center' },
    { label: 'Trạng thái', key: 'status', align: 'text-center' },
    { label: 'Người duyệt', key: 'reviewed_by_name' },
    { label: 'Lý do', key: 'reason' },
    { label: 'Ghi chú duyệt', key: 'review_note' },
  ]

  if (!props.can_view_all) {
    return baseColumns
  }

  return [
    { label: 'Nhân viên', key: 'employee_name' },
    { label: 'Mã NV', key: 'employee_code' },
    { label: 'Phòng ban', key: 'department_name' },
    ...baseColumns,
  ]
})

const summaryCards = computed(() => [
  { label: 'Tổng bản ghi công', value: props.summary.total_records ?? 0 },
  { label: 'Ngày làm', value: props.summary.present_records ?? 0 },
  { label: 'Nghỉ phép', value: props.summary.leave_records ?? 0 },
  { label: 'Nghỉ không lương', value: props.summary.unpaid_leave_records ?? 0 },
  { label: 'Lịch nghỉ theo phân ca', value: props.summary.day_off_records ?? 0, hint: 'Không tính là bản ghi công' },
  {
    label: 'Công cần xác minh',
    value: props.summary.needs_verification_records ?? props.summary.action_required_records ?? 0,
    hint: verificationHint.value,
  },
  { label: 'Vi phạm', value: props.summary.violation_records ?? 0 },
  { label: 'Giờ làm thực tế', value: formatMinutes(props.summary.total_actual_worked_minutes ?? props.summary.total_worked_minutes ?? 0) },
])

const verificationHint = computed(() => {
  const missingCheck = props.summary.needs_verification_missing_check_records ?? props.summary.missing_check_records ?? 0
  const missingAttendance = props.summary.needs_verification_missing_attendance_records ?? props.summary.missing_attendance_records ?? 0
  const timeViolation = props.summary.needs_verification_time_violation_records ?? 0

  return `Thiếu check: ${missingCheck}, vắng: ${missingAttendance}, giờ công: ${timeViolation}`
})

const exportQuery = computed(() => ({
  month: filterForm.month,
  year: filterForm.year,
  employee_profile_id: filterForm.employee_profile_id,
  keyword: filterForm.keyword,
}))

const exportExcelUrl = computed(() => route('attendance.reports.export.excel', exportQuery.value))
const exportPdfUrl = computed(() => route('attendance.reports.export.pdf', exportQuery.value))
const monthLockPeriodLabel = computed(() => `Tháng ${String(filterForm.month).padStart(2, '0')}/${filterForm.year}`)

function applyFilters() {
  router.get(route('attendance.reports'), exportQuery.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

function toggleMonthLock(action) {
  monthLockConfirm.open = true
  monthLockConfirm.action = action
  monthLockConfirm.note = props.month_lock?.note || ''
  monthLockForm.clearErrors()
}

function closeMonthLockConfirm() {
  if (monthLockForm.processing) return
  monthLockConfirm.open = false
}

function confirmMonthLockPreview() {
  monthLockForm.month = filterForm.month
  monthLockForm.year = filterForm.year
  monthLockForm.note = monthLockConfirm.note

  monthLockForm.post(
    route(monthLockConfirm.action === 'lock' ? 'attendance.month-locks.lock' : 'attendance.month-locks.unlock'),
    {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        monthLockConfirm.open = false
      },
    }
  )
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

function formatDayStatus(item) {
  if (item?.day_status_label) {
    return item.day_status_label
  }

  const value = typeof item === 'string' ? item : item?.day_status

  if (value === 'unpaid_leave' && item?.violation_status === 'missing_attendance') {
    return 'Vắng mặt'
  }

  const labels = {
    present: 'Đi làm',
    leave: 'Nghỉ phép',
    unpaid_leave: 'Nghỉ không lương',
    holiday_paid: 'Lễ có lương',
    day_off: 'Nghỉ theo phân ca',
    business_trip: 'Công tác',
    absent: 'Vắng mặt',
  }
  return labels[value] || '-'
}

function formatViolationStatus(value) {
  const labels = {
    late: 'Đi muộn',
    early_leave: 'Về sớm',
    late_early: 'Đi muộn / về sớm',
    missing_check_in: 'Thiếu check in',
    missing_check_out: 'Thiếu check out',
    missing_attendance: 'Tự ý nghỉ / chưa có đơn',
  }
  return labels[value] || '-'
}

function formatApprovalStatus(value) {
  const labels = {
    pending: 'Chờ duyệt',
    needs_verification: 'Cần xác minh',
    not_required: 'Không cần duyệt',
    approved: 'Đã duyệt',
    rejected: 'Từ chối',
    cancelled: 'Đã hủy',
  }
  return labels[value] || '-'
}

function dayStatusClass(value) {
  const classes = {
    present: 'bg-emerald-50 text-emerald-700',
    leave: 'bg-sky-50 text-sky-700',
    unpaid_leave: 'bg-rose-50 text-rose-700',
    holiday_paid: 'bg-cyan-50 text-cyan-700',
    day_off: 'bg-slate-50 text-slate-600',
    business_trip: 'bg-violet-50 text-violet-700',
    absent: 'bg-slate-100 text-slate-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}

function violationStatusClass(value) {
  const classes = {
    late: 'bg-amber-50 text-amber-700',
    early_leave: 'bg-orange-50 text-orange-700',
    late_early: 'bg-orange-50 text-orange-700',
    missing_check_in: 'bg-yellow-50 text-yellow-700',
    missing_check_out: 'bg-yellow-50 text-yellow-700',
    missing_attendance: 'bg-slate-100 text-slate-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-500'
}

function approvalStatusClass(value) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700',
    needs_verification: 'bg-orange-50 text-orange-700',
    not_required: 'bg-slate-50 text-slate-600',
    approved: 'bg-emerald-50 text-emerald-700',
    rejected: 'bg-rose-50 text-rose-700',
    cancelled: 'bg-slate-100 text-slate-600',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}

function attendanceRowClass(item) {
  const displayStatus = item?.display_approval_status || item?.approval_status

  if (displayStatus === 'needs_verification') {
    return 'bg-orange-50/70 hover:bg-orange-50'
  }

  if (displayStatus === 'pending') {
    return 'bg-amber-50/60 hover:bg-amber-50'
  }

  return ''
}

function requestRowClass(item) {
  if (item?.status === 'pending') {
    return 'bg-amber-50/60 hover:bg-amber-50'
  }

  return ''
}
</script>
