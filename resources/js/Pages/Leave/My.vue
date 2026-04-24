<template>
  <Head title="Nghi phep cua toi" />

  <AdminLayout>
    <PageBreadcrumb title="Nghi phep cua toi" :items="[{ text: 'Cham cong', link: null }, { text: 'Nghi phep cua toi', link: null }]" />

    <section class="mb-6 rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
        <div>
          <div class="text-sm font-semibold text-gray-900">Tong quan nghi phep</div>
          <div class="mt-1 text-sm text-gray-500">
            Theo doi quy phep, don nghi da gui va tac dong toi cong theo tung nam.
          </div>

          <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-blue-700">So du hien tai</div>
            <div class="mt-2 text-2xl font-semibold text-blue-950">{{ headlineText }}</div>
            <div class="mt-2 text-sm text-blue-900">
              Cong thuc: <strong>{{ formatDays(summary.total_entitled) }}</strong> duoc huong
              - <strong>{{ formatDays(summary.total_used) }}</strong> da dung
              - <strong>{{ formatDays(summary.total_pending) }}</strong> dang cho duyet.
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
              <span
                v-for="badge in headlineBadges"
                :key="badge.label"
                :class="badgeClass(badge.tone)"
                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
              >
                {{ badge.label }}
              </span>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
          <div class="text-sm font-semibold text-gray-900">Bo loc</div>
          <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Nam</label>
              <select v-model="filterForm.year" class="form-input" @change="applyYearFilter">
                <option v-for="year in year_options" :key="year" :value="year">{{ year }}</option>
              </select>
            </div>

            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Loai nghi</label>
              <select v-model="filterForm.leave_type_name" class="form-input">
                <option value="">Tat ca</option>
                <option v-for="option in leaveTypeOptions" :key="option" :value="option">{{ option }}</option>
              </select>
            </div>

            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Trang thai don</label>
              <select v-model="filterForm.request_status" class="form-input">
                <option value="">Tat ca</option>
                <option v-for="option in requestStatusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
          </div>

          <div class="mt-4">
            <label class="mb-2 block text-sm font-medium text-gray-700">Tim nhanh</label>
            <input
              v-model.trim="filterForm.keyword"
              class="form-input"
              placeholder="Tim theo loai nghi, ly do, ghi chu, nguoi duyet..."
              type="text"
            >
          </div>

          <div class="mt-4 flex flex-wrap gap-2">
            <button class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" type="button" @click="clearLocalFilters">
              Xoa loc nang cao
            </button>
          </div>
        </div>
      </div>
    </section>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div v-for="card in summaryCards" :key="card.label" class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm">
        <div class="text-sm text-gray-500">{{ card.label }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
        <div v-if="card.hint" class="mt-1 text-xs leading-5 text-gray-500">{{ card.hint }}</div>
      </div>
    </div>

    <section class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">So du theo loai nghi</h3>
        <p class="mt-1 text-sm text-gray-500">
          Duoc huong = dau ky + cong them + dieu chinh. Con kha dung = duoc huong - da dung - dang cho duyet.
        </p>
      </div>

      <div v-if="filteredBalances.length" class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500">
              <th class="p-2">Loai nghi</th>
              <th class="p-2">Duoc huong</th>
              <th class="p-2">Dieu chinh</th>
              <th class="p-2">Da su dung</th>
              <th class="p-2">Dang cho duyet</th>
              <th class="p-2">Con kha dung</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in filteredBalances" :key="item.id" class="border-t">
              <td class="p-2">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="font-semibold text-gray-900">{{ item.leave_type_name }}</span>
                  <span
                    v-for="badge in balanceBadges(item)"
                    :key="`${item.id}-${badge.label}`"
                    :class="badgeClass(badge.tone)"
                    class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold"
                  >
                    {{ badge.label }}
                  </span>
                </div>
                <div class="mt-1 text-xs text-gray-500">
                  {{ item.leave_type_paid ? 'Co luong' : 'Khong luong' }}
                  <span v-if="item.leave_type_deducts_balance"> | Tru quy phep</span>
                  <span v-else> | Khong tru quy phep</span>
                </div>
              </td>
              <td class="p-2">
                <div>{{ formatDays(item.total_entitled) }}</div>
                <div class="text-xs text-gray-500">
                  Dau ky {{ formatDays(item.opening_balance) }} + cong them {{ formatDays(item.accrued_days) }}
                </div>
              </td>
              <td class="p-2">{{ formatSignedDays(item.adjusted_days) }}</td>
              <td class="p-2">{{ formatDays(item.used_days) }}</td>
              <td class="p-2">{{ formatDays(item.pending_days) }}</td>
              <td class="p-2">
                <div class="font-semibold text-emerald-700">{{ formatDays(item.available_days) }}</div>
                <div class="text-xs text-gray-500">{{ balanceAvailabilityText(item) }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm text-gray-500">
        Khong co du lieu so du phu hop voi bo loc hien tai.
      </div>
    </section>

    <section class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Lich su don nghi phep</h3>
        <p class="mt-1 text-sm text-gray-500">
          Xem ro trang thai duyet, tac dong toi cong va cach he thong dang giu/tru quy phep cho tung don.
        </p>
      </div>

      <DataTable
        :columns="requestColumns"
        :data="filteredLeaveRequests"
        :actions="requestActions"
        paginate
        :default-per-page="10"
        :per-page-options="[10, 20, 50]"
        empty-message="Khong co don nghi phep nao phu hop voi bo loc hien tai."
      >
        <template #cell-leave_type_name="{ item }">
          <div class="font-medium text-gray-900">{{ item.leave_type_name || item.leave_type || '-' }}</div>
          <div class="text-xs text-gray-500">{{ item.leave_type_paid ? 'Co luong' : 'Khong luong' }}</div>
        </template>
        <template #cell-period="{ item }">
          {{ requestPeriodLabel(item) }}
        </template>
        <template #cell-leave_duration="{ item }">
          {{ leaveDurationLabel(item) }}
        </template>
        <template #cell-status_label="{ item }">
          <span :class="approvalStatusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ item.status_label }}
          </span>
        </template>
        <template #cell-attendance_impact="{ item }">
          <div class="font-medium text-gray-900">{{ attendanceImpactLabel(item) }}</div>
          <div class="text-xs text-gray-500">{{ attendanceImpactHint(item) }}</div>
        </template>
        <template #cell-balance_effect="{ item }">
          <div class="font-medium text-gray-900">{{ balanceEffectLabel(item) }}</div>
          <div class="text-xs text-gray-500">{{ balanceEffectHint(item) }}</div>
        </template>
        <template #cell-submitted_at="{ item }">
          {{ formatDateTime(item.submitted_at) }}
        </template>
        <template #cell-reviewed_at="{ item }">
          {{ formatDateTime(item.reviewed_at) }}
        </template>
        <template #cell-attachment="{ item }">
          <a
            v-if="item.attachment_url"
            :href="item.attachment_url"
            class="text-sm font-medium text-blue-700 hover:text-blue-800 hover:underline"
            target="_blank"
            rel="noreferrer"
          >
            Xem tep
          </a>
          <span v-else>-</span>
        </template>
      </DataTable>
    </section>
    <ActionDialog ref="actionDialogRef" />
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'
import ActionDialog from '@/components/ui/ActionDialog.vue'
import { useActionDialog } from '@/composables/useActionDialog'

const LOW_BALANCE_THRESHOLD = 3

const props = defineProps({
  filters: { type: Object, required: true },
  year_options: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
  balances: { type: Array, default: () => [] },
  leave_requests: { type: Array, default: () => [] },
})
const { actionDialogRef, openConfirm } = useActionDialog()

const filterForm = reactive({
  year: Number(props.filters.year),
  leave_type_name: '',
  request_status: '',
  keyword: '',
})

const requestStatusOptions = [
  { value: 'pending', label: 'Cho duyet' },
  { value: 'approved', label: 'Da duyet' },
  { value: 'rejected', label: 'Tu choi' },
  { value: 'cancelled', label: 'Da huy' },
]

const leaveTypeOptions = computed(() => {
  const names = [
    ...props.balances.map((item) => item.leave_type_name),
    ...props.leave_requests.map((item) => item.leave_type_name),
  ]

  return Array.from(new Set(names.filter(Boolean))).sort((a, b) => a.localeCompare(b))
})

const normalizedKeyword = computed(() => normalizeText(filterForm.keyword))

const filteredBalances = computed(() => {
  return (props.balances || []).filter((item) => {
    if (filterForm.leave_type_name && item.leave_type_name !== filterForm.leave_type_name) {
      return false
    }

    if (!normalizedKeyword.value) {
      return true
    }

    return normalizeText([
      item.leave_type_name,
      item.leave_type_paid ? 'co luong' : 'khong luong',
    ].join(' ')).includes(normalizedKeyword.value)
  })
})

const filteredLeaveRequests = computed(() => {
  return (props.leave_requests || []).filter((item) => {
    if (filterForm.leave_type_name && item.leave_type_name !== filterForm.leave_type_name) {
      return false
    }

    if (filterForm.request_status && item.status !== filterForm.request_status) {
      return false
    }

    if (!normalizedKeyword.value) {
      return true
    }

    return normalizeText([
      item.leave_type_name,
      item.reason,
      item.review_note,
      item.reviewed_by_name,
      item.status_label,
      requestPeriodLabel(item),
    ].join(' ')).includes(normalizedKeyword.value)
  })
})

const summary = computed(() => {
  const totals = filteredBalances.value.reduce((carry, item) => {
    carry.total_opening += Number(item.opening_balance || 0)
    carry.total_accrued += Number(item.accrued_days || 0)
    carry.total_adjusted += Number(item.adjusted_days || 0)
    carry.total_entitled += Number(item.total_entitled || 0)
    carry.total_used += Number(item.used_days || 0)
    carry.total_pending += Number(item.pending_days || 0)

    return carry
  }, {
    total_opening: 0,
    total_accrued: 0,
    total_adjusted: 0,
    total_entitled: 0,
    total_used: 0,
    total_pending: 0,
  })

  const rawAvailable = totals.total_entitled - totals.total_used - totals.total_pending

  return {
    ...totals,
    raw_available: rawAvailable,
    total_available: Math.max(0, rawAvailable),
  }
})

const headlineText = computed(() => {
  if (!filteredBalances.value.length) {
    return 'Chua co du lieu nghi phep trong scope hien tai'
  }

  if (summary.value.raw_available < 0) {
    return `Ban dang vuot phep ${formatDays(Math.abs(summary.value.raw_available))}`
  }

  return `Ban con ${formatDays(summary.value.total_available)} phep (sau khi tru cac don dang cho)`
})

const headlineBadges = computed(() => {
  const badges = []

  if (summary.value.raw_available < 0) {
    badges.push({ label: 'Vuot phep', tone: 'danger' })
  } else if (summary.value.total_available > 0 && summary.value.total_available <= LOW_BALANCE_THRESHOLD) {
    badges.push({ label: 'Sap het phep', tone: 'warning' })
  }

  if (summary.value.total_pending > 0) {
    badges.push({ label: `Dang giu ${formatDays(summary.value.total_pending)}`, tone: 'info' })
  }

  if (!badges.length) {
    badges.push({ label: 'Con du quy phep', tone: 'success' })
  }

  return badges
})

const summaryCards = computed(() => [
  {
    label: 'Duoc huong',
    value: formatDays(summary.value.total_entitled),
    hint: `Dau ky ${formatDays(summary.value.total_opening)} + cong them ${formatDays(summary.value.total_accrued)} + dieu chinh ${formatSignedDays(summary.value.total_adjusted)}`,
  },
  {
    label: 'Da su dung',
    value: formatDays(summary.value.total_used),
    hint: 'Chi tinh cac don nghi da duoc duyet.',
  },
  {
    label: 'Dang cho duyet',
    value: formatDays(summary.value.total_pending),
    hint: 'So ngay dang tam giu quy phep cho don pending.',
  },
  {
    label: 'Con kha dung',
    value: formatDays(summary.value.total_available),
    hint: `Duoc huong - da dung - cho duyet = ${formatDays(summary.value.total_available)}`,
  },
])

const requestColumns = [
  { label: 'Loai nghi', key: 'leave_type_name' },
  { label: 'Khoang nghi', key: 'period' },
  { label: 'Thoi luong', key: 'leave_duration', align: 'text-center' },
  { label: 'Trang thai', key: 'status_label', align: 'text-center' },
  { label: 'Anh huong toi cong', key: 'attendance_impact' },
  { label: 'Tac dong quy phep', key: 'balance_effect' },
  { label: 'Gui luc', key: 'submitted_at' },
  { label: 'Duyet luc', key: 'reviewed_at' },
  { label: 'Nguoi duyet', key: 'reviewed_by_name' },
  { label: 'Minh chung', key: 'attachment', align: 'text-center' },
  { label: 'Ly do', key: 'reason' },
  { label: 'Ghi chu duyet', key: 'review_note' },
]

const requestActions = [
  {
    label: 'Huy',
    buttonProps: {
      title: 'Huy don nghi phep dang cho duyet',
      class: 'border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 shadow-sm hover:border-rose-300 hover:bg-rose-100 hover:text-rose-800',
    },
    hidden: (item) => !canCancelLeaveRequest(item),
    onClick: (item) => cancelLeaveRequest(item),
  },
]

function applyYearFilter() {
  router.get(route('leave.mine'), {
    year: filterForm.year,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function clearLocalFilters() {
  filterForm.leave_type_name = ''
  filterForm.request_status = ''
  filterForm.keyword = ''
}

function canCancelLeaveRequest(item) {
  return item?.status === 'pending' && !!item?.approval_request_id
}

async function cancelLeaveRequest(item) {
  if (!canCancelLeaveRequest(item)) return

  const confirmed = await openConfirm({
    title: 'Hủy đơn nghỉ phép',
    message: 'Bạn có chắc muốn hủy đơn nghỉ phép đang chờ duyệt này?',
    okText: 'Xác nhận hủy',
    cancelText: 'Đóng',
    variant: 'danger',
    eyebrow: 'Xác nhận',
  })
  if (!confirmed) return

  router.delete(route('attendance.requests.destroy', item.approval_request_id), {
    preserveScroll: true,
  })
}

function requestPeriodLabel(item) {
  if (item?.period) return item.period

  const fromDate = formatDate(item?.from_date)
  const toDate = formatDate(item?.to_date)
  if (fromDate !== '-' && toDate !== '-') {
    return fromDate === toDate ? fromDate : `${fromDate} - ${toDate}`
  }

  return formatDate(item?.request_date)
}

function leaveDurationLabel(item) {
  if (!item?.leave_duration_type) {
    return item?.leave_days ? `${formatNumber(item.leave_days)} ngay` : '-'
  }

  if (item.leave_duration_type === 'full_day') return 'Ca ngay'
  if (item.leave_duration_type === 'half_day') return 'Nua ngay'
  if (item.leave_duration_type === 'hourly') {
    return `Theo gio${item.leave_hours ? ` (${formatNumber(item.leave_hours)} gio)` : ''}`
  }

  return item.leave_duration_type
}

function attendanceImpactLabel(item) {
  const statusPrefix = item?.status === 'approved' ? 'Da ghi nhan:' : 'Neu duyet:'

  if (item?.leave_type === 'unpaid') {
    return `${statusPrefix} nghi khong luong`
  }

  return `${statusPrefix} nghi co luong`
}

function attendanceImpactHint(item) {
  if (item?.leave_duration_type === 'hourly') {
    return 'Anh huong cong theo so gio nghi duoc duyet.'
  }

  if (item?.leave_duration_type === 'half_day') {
    return 'Neu duyet se anh huong nua cong trong ngay nghi.'
  }

  return 'Neu duyet se cap nhat trang thai ngay cong theo loai nghi.'
}

function balanceEffectLabel(item) {
  if (!item?.leave_type_deducts_balance) {
    return 'Khong tru quy phep'
  }

  if (item?.status === 'pending') {
    return `Tam giu ${formatDays(item.leave_days || 0)}`
  }

  if (item?.status === 'approved') {
    return `Da tru ${formatDays(item.leave_days || 0)}`
  }

  return 'Khong tru / da hoan lai'
}

function balanceEffectHint(item) {
  if (!item?.leave_type_deducts_balance) {
    return 'Loai nghi nay khong lam giam quy phep.'
  }

  if (item?.status === 'pending') {
    return 'He thong dang giu cho quy phep trong luc cho duyet.'
  }

  if (item?.status === 'approved') {
    return 'So ngay nay da duoc chuyen thanh da su dung.'
  }

  return 'Neu don bi tu choi/huy, quy phep se khong bi tru.'
}

function balanceAvailabilityText(item) {
  const rawRemaining = Number(item.total_entitled || 0) - Number(item.used_days || 0) - Number(item.pending_days || 0)

  if (rawRemaining < 0) {
    return `Vuot ${formatDays(Math.abs(rawRemaining))} so voi quy hien co`
  }

  if (Number(item.available_days || 0) <= LOW_BALANCE_THRESHOLD && Number(item.available_days || 0) > 0) {
    return 'Sap cham nguong het phep'
  }

  if (Number(item.pending_days || 0) > 0) {
    return 'Da tru san ca phan dang cho duyet'
  }

  return 'Con trong han muc kha dung'
}

function balanceBadges(item) {
  const rawRemaining = Number(item.total_entitled || 0) - Number(item.used_days || 0) - Number(item.pending_days || 0)
  const badges = []

  if (rawRemaining < 0) {
    badges.push({ label: 'Vuot phep', tone: 'danger' })
  } else if (Number(item.available_days || 0) > 0 && Number(item.available_days || 0) <= LOW_BALANCE_THRESHOLD) {
    badges.push({ label: 'Sap het phep', tone: 'warning' })
  }

  if (Number(item.pending_days || 0) > 0) {
    badges.push({ label: 'Dang giu quota', tone: 'info' })
  }

  return badges
}

function badgeClass(tone) {
  return {
    success: 'bg-emerald-50 text-emerald-700',
    info: 'bg-blue-50 text-blue-700',
    warning: 'bg-amber-50 text-amber-700',
    danger: 'bg-rose-50 text-rose-700',
  }[tone] || 'bg-slate-50 text-slate-700'
}

function formatDays(value) {
  return `${formatNumber(value)} ngay`
}

function formatSignedDays(value) {
  const number = Number(value || 0)
  if (number > 0) return `+${formatNumber(number)} ngay`
  if (number < 0) return `${formatNumber(number)} ngay`
  return '0 ngay'
}

function formatNumber(value) {
  const number = Number(value || 0)
  return Number.isInteger(number) ? `${number}` : number.toFixed(1)
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

function normalizeText(value) {
  return String(value || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
}

function approvalStatusClass(value) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700',
    approved: 'bg-emerald-50 text-emerald-700',
    rejected: 'bg-rose-50 text-rose-700',
    cancelled: 'bg-slate-100 text-slate-600',
    not_required: 'bg-slate-50 text-slate-600',
  }

  return classes[value] || 'bg-slate-50 text-slate-700'
}
</script>

<style scoped>
.form-input {
  width: 100%;
  border-radius: 0.75rem;
  border: 1px solid #d1d5db;
  padding: 0.625rem 0.75rem;
  color: #111827;
  outline: none;
}

.form-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgb(37 99 235 / 0.15);
}
</style>
