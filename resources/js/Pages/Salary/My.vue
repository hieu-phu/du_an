<template>
  <Head title="Bảng lương cá nhân" />

  <AdminLayout>
    <PageBreadcrumb title="Bảng lương cá nhân" :items="[{ text: 'Lương', link: null }, { text: 'Bảng lương cá nhân', link: null }]" />

    <div class="space-y-6">
      <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
          <div>
            <div class="text-sm font-semibold text-gray-900">Bộ lọc kỳ lương</div>
            <div class="mt-1 text-sm text-gray-500">Chọn tháng năm bằng lịch để xem bảng lương cá nhân.</div>
          </div>
          <div class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
            Đang xem: {{ currentPeriodLabel }}
          </div>
        </div>

        <div class="mt-5 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
          <div class="w-full max-w-xl">
            <InputDate
              v-model="filterForm.period"
              label="Chọn tháng năm"
              placeholder="Chọn tháng năm"
              :clearable="false"
              :config="periodPickerConfig"
            />
          </div>
          <div class="flex flex-wrap justify-end gap-2">
            <button
              class="rounded-2xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
              type="button"
              @click="jumpToCurrentPeriod"
            >
              Tháng này
            </button>
            <button
              class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
              type="button"
              @click="exportPayslipPdf"
            >
              Xuất PDF
            </button>
            <button class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700" type="button" @click="printPayslip">
                In phiếu lương
            </button>
          </div>
        </div>

        <div
          v-if="periodStatus"
          class="mt-4 rounded-2xl px-4 py-3 text-sm"
          :class="periodStatus.is_locked ? 'border border-amber-200 bg-amber-50 text-amber-800' : 'border border-blue-200 bg-blue-50 text-blue-800'"
        >
          <div class="font-semibold">{{ periodStatus.status_label }}</div>
          <div class="mt-1">
            <template v-if="periodStatus.is_locked">
              Snapshot khóa lúc {{ formatDateTime(periodStatus.locked_at) }} bởi {{ periodStatus.locked_by_name || 'Hệ thống' }}.
            </template>
            <template v-else>
              Kỳ lương hiện đang ở trạng thái tính dòng, dữ liệu sẽ cập nhật theo chấm công và điều chỉnh mới nhất.
            </template>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm lg:col-span-1">
          <h3 class="text-lg font-semibold text-gray-900">Thông tin nhân viên</h3>
          <dl class="mt-4 space-y-3 text-sm">
            <InfoRow label="Mã NV" :value="profile.employee_code" />
            <InfoRow label="Họ tên" :value="profile.name" />
            <InfoRow label="Phòng ban" :value="profile.department" />
            <InfoRow label="Chức vụ" :value="profile.position" />
            <InfoRow label="Loại HĐ" :value="employmentTypeLabel(profile.employment_type)" />
          </dl>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:col-span-3 xl:grid-cols-4">
          <SummaryCard label="Lương cơ bản" :value="formatCurrency(summary.base_salary)" />
          <SummaryCard label="Công duyệt" :value="`${formatNumber(summary.approved_work_units)} / ${formatNumber(summary.expected_work_days)}`" />
          <SummaryCard label="Thu nhập phát sinh" :value="formatCurrency(summary.gross_amount)" />
          <SummaryCard label="Số dư sau đối trừ" :value="formatCurrency(summary.net_amount)" tone="green" />
        </div>
      </section>

      <section class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 shadow-theme-sm">
        Thu nhập theo công là tiền của công đã làm và đã duyệt. "Khấu trừ do thiếu công" là phần thu nhập không được hưởng do chưa đủ công chuẩn trong kỳ đang tính.
      </section>

      <section v-if="showPayslip" id="payslip-print" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-theme-sm">
        <div class="bg-slate-950 px-6 py-5 text-white">
          <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-300">HRM System</p>
              <h2 class="mt-2 text-2xl font-bold">Phiếu lương cá nhân</h2>
              <p class="mt-1 text-sm text-slate-300">Kỳ lương: {{ payslipPeriod }}</p>
            </div>
            <div class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-right">
              <div class="text-xs uppercase tracking-wide text-slate-300">Số dư sau đối trừ</div>
              <div class="mt-1 text-2xl font-bold text-emerald-300">{{ formatCurrency(summary.net_amount) }}</div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-0 lg:grid-cols-3">
          <div class="border-b border-slate-200 p-6 lg:border-b-0 lg:border-r">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Nhân viên</h3>
            <dl class="mt-4 space-y-3 text-sm">
              <InfoRow label="Mã NV" :value="profile.employee_code" />
              <InfoRow label="Họ tên" :value="profile.name" />
              <InfoRow label="Phòng ban" :value="profile.department" />
              <InfoRow label="Chức vụ" :value="profile.position" />
              <InfoRow label="Loại HĐ" :value="employmentTypeLabel(profile.employment_type)" />
            </dl>
          </div>

          <div class="border-b border-slate-200 p-6 lg:border-b-0 lg:border-r">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Thu nhập</h3>
            <div class="mt-4 space-y-3">
              <PayslipLine label="Lương cơ bản" :value="formatCurrency(summary.base_salary)" />
              <PayslipLine label="Thu nhập theo công duyệt" :value="formatCurrency(summary.base_salary_amount)" />
              <PayslipLine label="Tiền tăng ca" :value="formatCurrency(summary.overtime_amount)" />
              <PayslipLine label="Phụ cấp" :value="formatCurrency(summary.allowance_amount)" />
              <PayslipLine label="Tổng thu nhập phát sinh" :value="formatCurrency(summary.gross_amount)" strong />
            </div>
          </div>

          <div class="p-6">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Công và tạm trú</h3>
            <div class="mt-4 space-y-3">
              <PayslipLine label="Công duyệt" :value="`${formatNumber(summary.approved_work_units)} / ${formatNumber(summary.expected_work_days)}`" />
              <PayslipLine label="Tăng ca duyệt" :value="formatMinutes(summary.approved_overtime_minutes)" />
              <PayslipLine label="Tiền chờ duyệt" :value="formatCurrency(summary.pending_amount)" />
              <PayslipLine label="Khấu trừ do thiếu công" :value="formatCurrency(summary.attendance_deduction_amount)" danger />
              <PayslipLine label="Khấu trừ khác tạm tính" :value="formatCurrency(summary.manual_deduction_amount)" danger />
            </div>
          </div>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-6 py-5">
          <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-3">
            <div>
              <div class="text-slate-500">Đơn giá ngày</div>
              <div class="mt-1 font-semibold text-slate-900">{{ formatCurrency(summary.daily_rate) }}</div>
            </div>
            <div>
              <div class="text-slate-500">Đơn giá giờ</div>
              <div class="mt-1 font-semibold text-slate-900">{{ formatCurrency(summary.hourly_rate) }}</div>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm">
              <div class="text-slate-500">Số dư sau đối trừ</div>
              <div class="mt-1 text-2xl font-bold text-emerald-700">{{ formatCurrency(summary.net_amount) }}</div>
            </div>
          </div>

          <div class="mt-8 grid grid-cols-2 gap-8 text-center text-sm text-slate-600">
            <div>
              <div class="font-semibold text-slate-900">Nhân viên</div>
              <div class="mt-14 border-t border-slate-300 pt-2">{{ profile.name || 'Ký và ghi rõ họ tên' }}</div>
            </div>
            <div>
              <div class="font-semibold text-slate-900">Phòng nhân sự</div>
              <div class="mt-14 border-t border-slate-300 pt-2">Ký và ghi rõ họ tên</div>
            </div>
          </div>
        </div>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Chi tiết tính lương</h3>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
          <BreakdownItem label="Đơn giá ngày" :value="formatCurrency(summary.daily_rate)" />
          <BreakdownItem label="Đơn giá giờ" :value="formatCurrency(summary.hourly_rate)" />
          <BreakdownItem label="Thu nhập theo công duyệt" :value="formatCurrency(summary.base_salary_amount)" />
          <BreakdownItem label="Tiền tăng ca" :value="formatCurrency(summary.overtime_amount)" />
          <BreakdownItem label="Phụ cấp" :value="formatCurrency(summary.allowance_amount)" />
          <BreakdownItem label="Tổng thu nhập phát sinh" :value="formatCurrency(summary.gross_amount)" />
          <BreakdownItem label="Công chờ duyệt" :value="formatNumber(summary.pending_work_units)" />
          <BreakdownItem label="Tiền chờ duyệt" :value="formatCurrency(summary.pending_amount)" />
          <BreakdownItem label="Công chưa tính" :value="formatNumber(summary.unpaid_work_units)" />
          <BreakdownItem label="Khấu trừ do thiếu công" :value="formatCurrency(summary.attendance_deduction_amount)" tone="red" />
          <BreakdownItem label="Khấu trừ khác tạm tính" :value="formatCurrency(summary.manual_deduction_amount)" tone="red" />
        </div>
      </section>

      <section v-if="summary.warnings?.length" class="rounded-xl border border-amber-200 bg-amber-50 p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-amber-900">Cảnh báo dữ liệu</h3>
        <ul class="mt-3 space-y-2 text-sm text-amber-800">
          <li v-for="warning in summary.warnings" :key="warning">{{ warning }}</li>
        </ul>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Công tính lương</h3>
        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-600">
                <th class="p-2">Ngày</th>
                <th class="p-2">Ca áp dụng</th>
                <th class="p-2">Phút làm</th>
                <th class="p-2">Công tính</th>
                <th class="p-2">Tiền công</th>
                <th class="p-2">Tăng ca</th>
                <th class="p-2">Hệ số OT</th>
                <th class="p-2">Tiền OT</th>
                <th class="p-2">Trạng thái ngày</th>
                <th class="p-2">Duyệt</th>
                <th class="p-2">Ghi chú</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in records" :key="item.id" class="border-t">
                <td class="p-2 font-medium text-gray-900">{{ formatDate(item.work_date) }}</td>
                <td class="p-2">
                  <div class="font-medium text-gray-900">{{ item.shift_name || '-' }}</div>
                  <div class="text-xs text-gray-500">{{ item.shift_time_range }}</div>
                  <div class="text-xs text-gray-500">Chuẩn {{ formatMinutes(item.shift_standard_minutes) }} / nửa công {{ formatMinutes(item.shift_half_day_minutes) }}</div>
                </td>
                <td class="p-2">{{ formatMinutes(item.worked_minutes) }}</td>
                <td class="p-2">{{ formatNumber(item.work_unit) }}</td>
                <td class="p-2 font-medium text-gray-900">{{ formatCurrency(item.payable_amount) }}</td>
                <td class="p-2">
                  <div>{{ formatMinutes(item.overtime_minutes) }}</div>
                  <div class="text-xs text-gray-500">{{ item.overtime_type_label || '-' }}</div>
                </td>
                <td class="p-2">
                  <div class="font-medium text-gray-900">{{ formatMultiplier(item.overtime_multiplier) }}</div>
                  <div class="text-xs text-gray-500">{{ overtimeRateLabel(item) }}</div>
                </td>
                <td class="p-2 font-medium text-gray-900">{{ formatCurrency(item.overtime_amount) }}</td>
                <td class="p-2">{{ dayStatusLabel(item.day_status, item.attendance_status) }}</td>
                <td class="p-2">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="approvalClass(item.approval_status)">
                    {{ approvalLabel(item.approval_status) }}
                  </span>
                </td>
                <td class="p-2">{{ item.approval_note || '-' }}</td>
              </tr>
              <tr v-if="!records.length">
                <td class="border-t p-4 text-center text-gray-500" colspan="11">Chưa có dữ liệu công trong kỳ.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Lịch sử điều chỉnh lương</h3>
        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-600">
                <th class="p-2">Ngày hiệu lực</th>
                <th class="p-2">Lương cũ</th>
                <th class="p-2">Lương mới</th>
                <th class="p-2">Ghi chú</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in salaryHistory" :key="item.id" class="border-t">
                <td class="p-2">{{ formatDate(item.effective_date) }}</td>
                <td class="p-2">{{ formatCurrency(item.old_salary, item.currency) }}</td>
                <td class="p-2 font-semibold text-gray-900">{{ formatCurrency(item.new_salary, item.currency) }}</td>
                <td class="p-2">{{ item.note || '-' }}</td>
              </tr>
              <tr v-if="!salaryHistory.length">
                <td class="border-t p-4 text-center text-gray-500" colspan="4">Chưa có lịch sử điều chỉnh lương.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, h, nextTick, reactive, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import InputDate from '@/components/forms/InputDate.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  profile: { type: Object, required: true },
  summary: { type: Object, required: true },
  records: { type: Array, default: () => [] },
  salaryHistory: { type: Array, default: () => [] },
  periodStatus: { type: Object, default: null },
})

const filterForm = reactive({
  month: Number(props.filters.month),
  year: Number(props.filters.year),
  period: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
})

const showPayslip = ref(false)
let lastAppliedPeriod = `${filterForm.year}-${filterForm.month}`

const payslipPeriod = computed(() => {
  const month = String(props.filters.month || '').padStart(2, '0')
  return `Tháng ${month}/${props.filters.year}`
})

const currentPeriodLabel = computed(() => {
  const date = parsePeriodValue(filterForm.period)
  if (!date) return `Tháng ${filterForm.month} / ${filterForm.year}`

  return new Intl.DateTimeFormat('vi-VN', {
    month: 'long',
    year: 'numeric',
  }).format(date)
})

const periodPickerConfig = {
  allowInput: false,
  dateFormat: 'Y-m-01',
  altInput: true,
  altFormat: 'm/Y',
  defaultDate: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
}

const InfoRow = (props) => h('div', { class: 'flex justify-between gap-3' }, [
  h('dt', { class: 'text-gray-500' }, props.label),
  h('dd', { class: 'text-right font-medium text-gray-900' }, props.value || '-'),
])
InfoRow.props = ['label', 'value']

const SummaryCard = (props) => h('div', { class: 'rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm' }, [
  h('div', { class: 'text-sm text-gray-500' }, props.label),
  h('div', { class: ['mt-2 text-2xl font-semibold', props.tone === 'green' ? 'text-emerald-700' : 'text-gray-900'] }, props.value),
])
SummaryCard.props = ['label', 'value', 'tone']

const BreakdownItem = (props) => h('div', { class: 'rounded-lg border border-gray-100 bg-gray-50 p-4' }, [
  h('div', { class: 'text-sm text-gray-500' }, props.label),
  h('div', { class: ['mt-1 text-lg font-semibold', props.tone === 'red' ? 'text-red-600' : 'text-gray-900'] }, props.value),
])
BreakdownItem.props = ['label', 'value', 'tone']

const PayslipLine = (props) => h('div', { class: 'flex items-start justify-between gap-3 text-sm' }, [
  h('span', { class: 'text-slate-500' }, props.label),
  h('span', {
    class: [
      'text-right font-semibold',
      props.strong ? 'text-slate-950' : props.danger ? 'text-red-600' : 'text-slate-800',
    ],
  }, props.value),
])
PayslipLine.props = ['label', 'value', 'strong', 'danger']

watch(
  () => filterForm.period,
  (value) => {
    const date = parsePeriodValue(value)
    if (!date) return

    const nextMonth = date.getMonth() + 1
    const nextYear = date.getFullYear()

    if (nextMonth === Number(filterForm.month) && nextYear === Number(filterForm.year)) {
      return
    }

    filterForm.month = nextMonth
    filterForm.year = nextYear
  }
)

watch(
  () => [Number(filterForm.month), Number(filterForm.year)],
  ([month, year]) => {
    if (!month || !year) return

    const nextPeriod = `${year}-${month}`
    if (nextPeriod === lastAppliedPeriod) return

    lastAppliedPeriod = nextPeriod
    applyFilters()
  }
)

function applyFilters() {
  router.get(route('salary.mine'), {
    month: filterForm.month,
    year: filterForm.year,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function jumpToCurrentPeriod() {
  const now = new Date()
  filterForm.month = now.getMonth() + 1
  filterForm.year = now.getFullYear()
  filterForm.period = buildPeriodValue(filterForm.year, filterForm.month)
}

function exportPayslipPdf() {
  window.location.href = route('salary.mine.export.pdf', {
    month: filterForm.month,
    year: filterForm.year,
  })
}

async function printPayslip() {
  showPayslip.value = true

  await nextTick()

  const hidePayslip = () => {
    showPayslip.value = false
    window.removeEventListener('afterprint', hidePayslip)
  }

  window.addEventListener('afterprint', hidePayslip)
  window.print()
}

function formatCurrency(value, currency = props.profile.currency || 'VND') {
  const amount = Number(value || 0)
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency,
    maximumFractionDigits: 0,
  }).format(amount)
}

function formatNumber(value) {
  return new Intl.NumberFormat('vi-VN', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(Number(value || 0))
}

function formatMultiplier(value) {
  const amount = Number(value || 0)
  return amount > 0 ? `${amount.toFixed(1)}x` : '-'
}

function overtimeRateLabel(item) {
  if (Number(item.overtime_hourly_rate || 0) > 0) {
    return `Đơn giá danh mục: ${formatCurrency(item.overtime_hourly_rate)}/giờ`
  }

  if (Number(item.overtime_multiplier || 0) > 0) {
    return `Theo ${item.overtime_type_label || 'loại ngày'}`
  }

  return '-'
}

function formatDate(value) {
  if (!value) return '-'
  const [year, month, day] = String(value).slice(0, 10).split('-')
  return `${day}/${month}/${year}`
}

function formatDateTime(value) {
  if (!value) return '-'
  const [date, time] = String(value).split(' ')
  return `${formatDate(date)} ${time || ''}`.trim()
}

function formatMinutes(minutes) {
  const value = Number(minutes || 0)
  if (value <= 0) return '0 phút'
  const hours = Math.floor(value / 60)
  const rest = value % 60
  if (!hours) return `${rest} phút`
  return rest ? `${hours} giờ ${rest} phút` : `${hours} giờ`
}

function employmentTypeLabel(type) {
  return {
    official: 'Chính thức',
    probation: 'Thử việc',
    intern: 'Thực tập',
    contractor: 'Hợp đồng',
  }[type] || type || '-'
}

function dayStatusLabel(dayStatus, attendanceStatus) {
  return {
    present: 'Đi làm',
    late: 'Đi muộn',
    early_leave: 'Về sớm',
    leave: 'Nghỉ có lương',
    holiday_paid: 'Ngày lễ có lương',
    unpaid_leave: 'Nghỉ không lương',
    business_trip: 'Công tác',
    missing_check_in: 'Thiếu check-in',
    missing_check_out: 'Thiếu check-out',
    absent: 'Vắng',
  }[dayStatus] || {
    on_time: 'Đúng giờ',
    late: 'Đi muộn',
    absent: 'Vắng',
  }[attendanceStatus] || '-'
}

function approvalLabel(status) {
  return {
    approved: 'Đã duyệt',
    pending: 'Chờ duyệt',
    rejected: 'Từ chối',
  }[status] || '-'
}

function approvalClass(status) {
  return {
    approved: 'bg-green-100 text-green-700',
    pending: 'bg-amber-100 text-amber-700',
    rejected: 'bg-red-100 text-red-700',
  }[status] || 'bg-gray-100 text-gray-600'
}

function buildPeriodValue(year, month) {
  if (!year || !month) return ''
  return `${year}-${String(month).padStart(2, '0')}-01`
}

function parsePeriodValue(value) {
  if (!value) return null
  const date = value instanceof Date ? value : new Date(value)
  return Number.isNaN(date.getTime()) ? null : date
}
</script>

<style scoped>
.form-input {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 0.625rem 0.75rem;
  color: #111827;
  outline: none;
}

.form-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgb(37 99 235 / 0.15);
}

@media print {
  :global(body) {
    background: #fff !important;
  }

  :global(body *) {
    visibility: hidden !important;
  }

  #payslip-print,
  #payslip-print * {
    visibility: visible !important;
  }

  #payslip-print {
    position: absolute;
    inset: 0 auto auto 0;
    width: 100%;
    border: 0 !important;
    box-shadow: none !important;
  }
}
</style>
