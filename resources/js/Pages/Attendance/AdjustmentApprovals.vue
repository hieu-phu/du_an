<template>
  <Head title="Duyet dieu chinh cong" />

  <AdminLayout>
    <PageBreadcrumb title="Duyet dieu chinh cong" :items="[{ text: 'Cham cong', link: null }, { text: 'Duyet dieu chinh', link: null }]" />

    <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <div class="text-sm font-semibold text-gray-900">Bo loc ky duyet dieu chinh</div>
          <div class="mt-1 text-sm text-gray-500">Chon thang nam bang lich va loc them theo nhan vien neu can.</div>
        </div>
        <div class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
          Dang xem: {{ currentPeriodLabel }}
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(280px,0.8fr)_auto]">
        <div class="max-w-xl">
          <InputDate
            v-model="filterForm.period"
            label="Chon thang nam"
            placeholder="Chon thang nam"
            :clearable="false"
            :config="periodPickerConfig"
          />
        </div>
        <div>
          <FormSelect
            v-model="filterForm.employee_profile_id"
            :options="employeeOptionItems"
            label="Nhan vien"
            placeholder="Tat ca nhan vien"
            :searchable="true"
            :can-clear="false"
            :show-optional-label="false"
          />
        </div>
        <div class="flex items-center xl:justify-end">
          <button
            type="button"
            class="rounded-2xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="jumpToCurrentPeriod"
          >
            Thang nay
          </button>
        </div>
      </div>

      <h3 class="mb-3 text-lg font-semibold text-gray-900">Yeu cau dang cho duyet</h3>
      <div class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left">
              <th class="p-2">Nhan vien</th>
              <th class="p-2">Ngay cong</th>
              <th class="p-2">Check-in cu / moi</th>
              <th class="p-2">Check-out cu / moi</th>
              <th class="p-2">Sau dieu chinh</th>
              <th class="p-2">Ly do</th>
              <th class="p-2">Tac vu</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in adjustments" :key="item.id" class="border-t">
              <td class="p-2">{{ item.employee_name }} ({{ item.employee_code }})</td>
              <td class="p-2">{{ formatDate(item.work_date) }}</td>
              <td class="p-2">{{ formatAdjustmentChange(item.old_check_in_at, item.new_check_in_at) }}</td>
              <td class="p-2">{{ formatAdjustmentChange(item.old_check_out_at, item.new_check_out_at) }}</td>
              <td class="p-2">
                <div>Vao: {{ formatDateTime(finalAdjustmentCheckIn(item)) }}</div>
                <div>Ra: {{ formatDateTime(finalAdjustmentCheckOut(item)) }}</div>
              </td>
              <td class="p-2">{{ item.reason }}</td>
              <td class="p-2">
                <div class="flex gap-2">
                  <button
                    class="rounded bg-emerald-600 px-3 py-1 text-white disabled:opacity-60"
                    type="button"
                    :disabled="processingId === item.id"
                    @click="approve(item)"
                  >
                    Duyet
                  </button>
                  <button
                    class="rounded bg-rose-600 px-3 py-1 text-white disabled:opacity-60"
                    type="button"
                    :disabled="processingId === item.id"
                    @click="reject(item)"
                  >
                    Tu choi
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!adjustments.length">
              <td class="p-4 text-center text-gray-500" colspan="7">Khong co yeu cau dieu chinh cong can duyet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <h3 class="mb-4 text-lg font-semibold text-gray-900">Lich su duyet dieu chinh cong</h3>

      <div class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left">
              <th class="p-2">Nhan vien</th>
              <th class="p-2">Ngay cong</th>
              <th class="p-2">Check-in cu / moi</th>
              <th class="p-2">Check-out cu / moi</th>
              <th class="p-2">Sau dieu chinh</th>
              <th class="p-2">Trang thai</th>
              <th class="p-2">Nguoi duyet</th>
              <th class="p-2">Ghi chu</th>
              <th class="p-2">Duyet luc</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in reviewed_adjustments" :key="item.id" class="border-t">
              <td class="p-2">{{ item.employee_name }} ({{ item.employee_code }})</td>
              <td class="p-2">{{ formatDate(item.work_date) }}</td>
              <td class="p-2">{{ formatAdjustmentChange(item.old_check_in_at, item.new_check_in_at) }}</td>
              <td class="p-2">{{ formatAdjustmentChange(item.old_check_out_at, item.new_check_out_at) }}</td>
              <td class="p-2">
                <div>Vao: {{ formatDateTime(finalAdjustmentCheckIn(item)) }}</div>
                <div>Ra: {{ formatDateTime(finalAdjustmentCheckOut(item)) }}</div>
              </td>
              <td class="p-2">
                <span :class="statusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
                  {{ item.status_label || formatStatus(item.status) }}
                </span>
              </td>
              <td class="p-2">{{ item.reviewed_by_name || '-' }}</td>
              <td class="p-2">{{ item.review_note || '-' }}</td>
              <td class="p-2">{{ formatDateTime(item.reviewed_at) }}</td>
            </tr>
            <tr v-if="!reviewed_adjustments.length">
              <td class="p-4 text-center text-gray-500" colspan="9">Chua co lich su duyet dieu chinh cong.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import InputDate from '@/components/forms/InputDate.vue'
import FormSelect from '@/components/forms/FormSelect.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  employees: { type: Array, default: () => [] },
  adjustments: { type: Array, default: () => [] },
  reviewed_adjustments: { type: Array, default: () => [] },
})

const processingId = ref(null)

const filterForm = reactive({
  month: Number(props.filters.month),
  year: Number(props.filters.year),
  period: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
  employee_profile_id: props.filters.employee_profile_id ?? null,
})

let lastAppliedFilterKey = `${filterForm.year}-${filterForm.month}-${filterForm.employee_profile_id ?? 'all'}`

const currentPeriodLabel = computed(() => {
  const date = parsePeriodValue(filterForm.period)
  if (!date) return `Thang ${filterForm.month} / ${filterForm.year}`

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

const employeeOptionItems = computed(() => [
  { value: null, label: 'Tat ca nhan vien' },
  ...(props.employees || []).map((employee) => ({
    value: employee.id,
    label: employee.label,
  })),
])

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
  () => [Number(filterForm.month), Number(filterForm.year), filterForm.employee_profile_id],
  ([month, year, employeeProfileId]) => {
    if (!month || !year) return

    const nextFilterKey = `${year}-${month}-${employeeProfileId ?? 'all'}`
    if (nextFilterKey === lastAppliedFilterKey) return

    lastAppliedFilterKey = nextFilterKey
    applyFilter()
  }
)

const applyFilter = () => {
  router.get(route('attendance.adjustments.approvals'), {
    month: filterForm.month,
    year: filterForm.year,
    employee_profile_id: filterForm.employee_profile_id,
  }, { preserveState: true, preserveScroll: true })
}

function jumpToCurrentPeriod() {
  const now = new Date()
  filterForm.month = now.getMonth() + 1
  filterForm.year = now.getFullYear()
  filterForm.period = buildPeriodValue(filterForm.year, filterForm.month)
}

const approve = (item) => {
  const confirmed = window.confirm(`Duyet dieu chinh cong ngay ${formatDate(item.work_date)} cho ${item.employee_name}?`)
  if (!confirmed) return

  const note = window.prompt('Ghi chu duyet', '') ?? ''
  processingId.value = item.id
  router.post(route('attendance.adjustments.approve', item.id), { note }, {
    preserveScroll: true,
    onFinish: () => {
      processingId.value = null
    },
  })
}

const reject = (item) => {
  const note = window.prompt('Ly do tu choi', '')
  if (note === null) return

  const confirmed = window.confirm(`Xac nhan TU CHOI yeu cau dieu chinh cong ngay ${formatDate(item.work_date)}? Cong cua nhan vien se giu nguyen.`)
  if (!confirmed) return

  processingId.value = item.id
  router.post(route('attendance.adjustments.reject', item.id), { note }, {
    preserveScroll: true,
    onFinish: () => {
      processingId.value = null
    },
  })
}

function normalizeDateValue(value) {
  if (!value) return ''
  return String(value).replace(' ', 'T')
}

function formatDate(value) {
  if (!value) return '-'
  const date = new Date(normalizeDateValue(value))
  return Number.isNaN(date.getTime()) ? '-' : date.toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  const date = new Date(normalizeDateValue(value))
  return Number.isNaN(date.getTime()) ? '-' : date.toLocaleString('vi-VN')
}

function formatAdjustmentChange(oldValue, newValue) {
  const oldText = formatDateTime(oldValue)
  if (!newValue) {
    return `${oldText} -> Giu nguyen`
  }

  return `${oldText} -> ${formatDateTime(newValue)}`
}

function finalAdjustmentCheckIn(item) {
  return item?.new_check_in_at || item?.old_check_in_at || ''
}

function finalAdjustmentCheckOut(item) {
  return item?.new_check_out_at || item?.old_check_out_at || ''
}

function formatStatus(value) {
  const labels = {
    pending: 'Cho duyet',
    approved: 'Da duyet',
    rejected: 'Tu choi',
  }
  return labels[value] || '-'
}

function statusClass(value) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700',
    approved: 'bg-emerald-50 text-emerald-700',
    rejected: 'bg-rose-50 text-rose-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
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
