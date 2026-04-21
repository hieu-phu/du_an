<template>
  <Head title="Cong cua toi" />

  <AdminLayout>
    <PageBreadcrumb title="Cong cua toi" :items="[{ text: 'Cham cong', link: null }, { text: 'Cong cua toi', link: null }]" />

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div v-for="card in summaryCards" :key="card.label" class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm">
        <div class="text-sm text-gray-500">{{ card.label }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
      </div>
    </div>

    <div class="mb-6 rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <div class="text-sm font-semibold text-gray-900">Bo loc ky cham cong</div>
          <div class="mt-1 text-sm text-gray-500">Chuyen nhanh qua tung thang hoac chon thang, nam cu the de xem du lieu.</div>
        </div>
        <div class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
          Dang xem: {{ currentPeriodLabel }}
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_auto]">
        <div class="max-w-xl">
          <InputDate
            v-model="filterForm.period"
            label="Chon thang nam"
            placeholder="Chon thang nam"
            :clearable="false"
            :config="periodPickerConfig"
          />
        </div>

        <div class="flex flex-wrap items-center gap-2 xl:justify-end">
          <button
            type="button"
            class="rounded-2xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="jumpToCurrentPeriod"
          >
            Thang nay
          </button>
        </div>
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Gui don lien quan cham cong</h3>
      </div>

      <div v-if="formError" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ formError }}
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700">Loai don</label>
          <select v-model="requestForm.request_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Chon loai don</option>
            <option v-for="type in request_types" :key="type.value" :value="type.value">{{ requestTypeLabel(type.value) }}</option>
          </select>
          <p v-if="requestForm.errors.request_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.request_type }}</p>
        </div>

        <InputDate
          v-if="showSingleDate"
          v-model="requestForm.request_date"
          :label="singleDateLabel"
          placeholder="Chon ngay"
          :error="requestForm.errors.request_date"
          :config="datePickerConfig"
        />

        <InputDate
          v-if="showDateRange"
          v-model="requestForm.from_date"
          label="Tu ngay"
          placeholder="Chon tu ngay"
          :error="requestForm.errors.from_date"
          :config="datePickerConfig"
        />

        <InputDate
          v-if="showDateRange"
          v-model="requestForm.to_date"
          label="Den ngay"
          placeholder="Chon den ngay"
          :error="requestForm.errors.to_date"
          :config="datePickerConfig"
        />

        <div v-if="requestForm.request_type === 'leave'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Loai nghi</label>
          <select v-model="requestForm.leave_type_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Chon loai nghi</option>
            <option v-for="type in leave_types" :key="type.id" :value="type.id">
              {{ type.name }} - {{ type.is_paid ? 'co luong' : 'khong luong' }}
            </option>
          </select>
          <p v-if="selectedLeaveType" class="mt-1 text-xs text-gray-500">
            {{ selectedLeaveType.deducts_balance ? `Con lai: ${formatWorkUnits(selectedLeaveAvailableDays)} ngay` : 'Loai nghi nay khong tru quy phep.' }}
          </p>
          <p v-if="requestForm.errors.leave_type_id || requestForm.errors.leave_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_type_id || requestForm.errors.leave_type }}</p>
        </div>

        <div v-if="requestForm.request_type === 'leave'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Thoi luong nghi</label>
          <select v-model="requestForm.leave_duration_type" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="full_day">Ca ngay</option>
            <option value="half_day">Nua ngay</option>
            <option value="hourly">Theo gio</option>
          </select>
          <p v-if="requestForm.errors.leave_duration_type" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_duration_type }}</p>
        </div>

        <div v-if="requestForm.request_type === 'leave' && requestForm.leave_duration_type === 'hourly'">
          <label class="mb-2 block text-sm font-medium text-gray-700">So gio nghi</label>
          <input v-model.number="requestForm.leave_hours" class="w-full rounded-lg border border-gray-300 px-3 py-2" type="number" min="0.5" max="24" step="0.5">
          <p v-if="requestForm.errors.leave_hours" class="mt-1 text-sm text-red-500">{{ requestForm.errors.leave_hours }}</p>
        </div>

        <div v-if="showLeaveAttachmentField">
          <label class="mb-2 block text-sm font-medium text-gray-700">Minh chung</label>
          <input ref="attachmentInput" class="w-full rounded-lg border border-gray-300 px-3 py-2" type="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" @change="setAttachment">
          <p v-if="selectedLeaveType?.requires_attachment" class="mt-1 text-xs text-amber-700">Loai nghi nay bat buoc co minh chung.</p>
          <p v-if="requestForm.errors.attachment" class="mt-1 text-sm text-red-500">{{ requestForm.errors.attachment }}</p>
        </div>

        <div v-if="requestForm.request_type === 'late_early'">
          <label class="mb-2 block text-sm font-medium text-gray-700">Loai de nghi</label>
          <select v-model="requestForm.requested_status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <option value="late">Xin di muon</option>
            <option value="early_leave">Xin ve som</option>
          </select>
          <p v-if="requestForm.errors.requested_status" class="mt-1 text-sm text-red-500">{{ requestForm.errors.requested_status }}</p>
        </div>

        <InputDate
          v-if="showTimeRange"
          v-model="requestForm.from_time"
          :label="fromTimeLabel"
          placeholder="Chon gio"
          :error="requestForm.errors.from_time"
          :config="timePickerConfig"
        />

        <InputDate
          v-if="showTimeRange"
          v-model="requestForm.to_time"
          :label="toTimeLabel"
          placeholder="Chon gio"
          :error="requestForm.errors.to_time"
          :config="timePickerConfig"
        />

        <InputDate
          v-if="requestForm.request_type === 'overtime'"
          v-model="requestForm.request_date"
          label="Ngay tang ca"
          placeholder="Chon ngay"
          :error="requestForm.errors.request_date"
          :config="datePickerConfig"
        />

        <div v-if="requestForm.request_type === 'overtime'" class="md:col-span-2 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
          <div class="font-semibold">Khung tang ca theo danh muc cham cong</div>
          <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-3">
            <div>
              <span class="text-blue-600">Ca ap dung:</span>
              <strong class="ml-1">{{ overtimeCatalog.shift_name || '-' }}</strong>
            </div>
            <div>
              <span class="text-blue-600">Thoi gian:</span>
              <strong class="ml-1">{{ overtimeWindowLabel }}</strong>
            </div>
            <div>
              <span class="text-blue-600">So gio tang ca:</span>
              <strong class="ml-1">{{ formatMinutes(overtimeCatalog.requested_minutes) }}</strong>
            </div>
          </div>
          <p v-if="!overtimeCatalog.start_time || !overtimeCatalog.end_time" class="mt-2 text-red-600">
            Ca lam hien tai chua cau hinh khung tang ca. Vui long lien he HR cap nhat danh muc cham cong.
          </p>
          <p v-if="requestForm.errors.start_at || requestForm.errors.end_at" class="mt-2 text-red-600">
            {{ requestForm.errors.start_at || requestForm.errors.end_at }}
          </p>
        </div>
      </div>

      <div class="mt-4">
        <label class="mb-2 block text-sm font-medium text-gray-700">Ly do</label>
        <textarea v-model="requestForm.reason" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Nhap ly do chi tiet"></textarea>
        <p v-if="requestForm.errors.reason" class="mt-1 text-sm text-red-500">{{ requestForm.errors.reason }}</p>
      </div>

      <div class="mt-4 flex justify-end">
        <button
          type="button"
          class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
          :disabled="requestForm.processing"
          @click="submitAttendanceRequest"
        >
          {{ requestForm.processing ? 'Dang gui...' : 'Gui don' }}
        </button>
      </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bang cong ca nhan</h3>
      </div>

      <DataTable :columns="columns" :data="records" empty-message="Chua co du lieu cham cong.">
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
          {{ shouldShowViolationMinutes(item) ? formatMinutes(item.late_minutes) : '-' }}
        </template>
        <template #cell-early_leave_minutes="{ item }">
          {{ shouldShowViolationMinutes(item) ? formatMinutes(item.early_leave_minutes) : '-' }}
        </template>
        <template #cell-overtime_minutes="{ item }">
          {{ formatMinutes(item.overtime_minutes) }}
        </template>
        <template #cell-attendance_status="{ item }">
          <span :class="statusClass(item)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatAttendanceStatus(item) }}
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
        <template #cell-request_presence_label="{ item }">
          <span :class="requestPresenceClass(item)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ item.request_presence_label || 'Khong co don' }}
          </span>
        </template>
      </DataTable>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Don cham cong da gui</h3>
        <p class="mt-1 text-sm text-gray-500">Theo doi cac don ban da gui va trang thai duyet hien tai.</p>
      </div>

      <DataTable :columns="requestColumns" :data="recent_requests" :actions="requestActions" empty-message="Ban chua gui don cham cong nao.">
        <template #cell-request_type_label="{ item }">
          {{ item.request_type_label || requestTypeLabel(item.request_type) }}
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

    <Modal :show="!!selectedSubmittedRequest" @close="closeSubmittedRequestDetail">
      <div v-if="selectedSubmittedRequest" class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">Chi tiet don da gui</h3>
        <div class="grid grid-cols-1 gap-3 text-sm text-gray-700 md:grid-cols-2">
          <div><span class="font-medium text-gray-900">Loai don:</span> {{ selectedSubmittedRequest.request_type_label || '-' }}</div>
          <div><span class="font-medium text-gray-900">Trang thai:</span> {{ selectedSubmittedRequest.status_label || '-' }}</div>
          <div><span class="font-medium text-gray-900">Ngay ap dung:</span> {{ formatDate(selectedSubmittedRequest.request_date) }}</div>
          <div><span class="font-medium text-gray-900">Khoang thoi gian:</span> {{ selectedSubmittedRequest.period || '-' }}</div>
          <div><span class="font-medium text-gray-900">Gui luc:</span> {{ formatDateTime(selectedSubmittedRequest.submitted_at) }}</div>
          <div><span class="font-medium text-gray-900">Nguoi duyet:</span> {{ selectedSubmittedRequest.reviewed_by_name || '-' }}</div>
          <div><span class="font-medium text-gray-900">Duyet luc:</span> {{ formatDateTime(selectedSubmittedRequest.reviewed_at) }}</div>
          <div class="md:col-span-2"><span class="font-medium text-gray-900">Ly do:</span> {{ selectedSubmittedRequest.reason || '-' }}</div>
          <div class="md:col-span-2"><span class="font-medium text-gray-900">Ghi chu duyet:</span> {{ selectedSubmittedRequest.review_note || '-' }}</div>
        </div>

        <div v-if="selectedSubmittedRequest.target_type === 'attendance'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thong tin don nghi/cham cong</div>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div><span class="font-medium text-gray-900">Tu ngay:</span> {{ formatDate(selectedSubmittedRequest.from_date) }}</div>
            <div><span class="font-medium text-gray-900">Den ngay:</span> {{ formatDate(selectedSubmittedRequest.to_date) }}</div>
            <div><span class="font-medium text-gray-900">Tu gio:</span> {{ selectedSubmittedRequest.from_time || '-' }}</div>
            <div><span class="font-medium text-gray-900">Den gio:</span> {{ selectedSubmittedRequest.to_time || '-' }}</div>
            <div><span class="font-medium text-gray-900">Loai nghi:</span> {{ selectedSubmittedRequest.leave_type_name || selectedSubmittedRequest.leave_type || '-' }}</div>
            <div><span class="font-medium text-gray-900">Thoi luong:</span> {{ leaveDurationLabel(selectedSubmittedRequest) }}</div>
            <div v-if="selectedSubmittedRequest.leave_days !== null && selectedSubmittedRequest.leave_days !== undefined">
              <span class="font-medium text-gray-900">So ngay nghi:</span> {{ formatWorkUnits(selectedSubmittedRequest.leave_days) }} ngay
            </div>
            <div v-if="selectedSubmittedRequest.requested_status">
              <span class="font-medium text-gray-900">Trang thai de nghi:</span> {{ selectedSubmittedRequest.requested_status }}
            </div>
          </div>

          <div v-if="selectedSubmittedRequest.request_type === 'leave'" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
            <div class="font-medium text-amber-900">Minh chung</div>
            <div v-if="selectedSubmittedRequest.attachment_url" class="mt-1">
              <a
                :href="selectedSubmittedRequest.attachment_url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-semibold text-blue-700 underline underline-offset-2"
              >
                Xem tep dinh kem
              </a>
            </div>
            <div v-else-if="selectedSubmittedRequest.leave_type_requires_attachment" class="mt-1 text-sm text-rose-700">
              Loai nghi nay yeu cau minh chung nhung don hien khong co tep dinh kem.
            </div>
            <div v-else class="mt-1 text-sm text-gray-500">
              Loai nghi nay khong yeu cau minh chung.
            </div>
          </div>
        </div>

        <div v-if="selectedSubmittedRequest.target_type === 'overtime'" class="mt-4 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
          <div class="mb-2 font-semibold text-gray-900">Thong tin tang ca</div>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div><span class="font-medium text-gray-900">Bat dau:</span> {{ formatDateTime(selectedSubmittedRequest.overtime_start_at) }}</div>
            <div><span class="font-medium text-gray-900">Ket thuc:</span> {{ formatDateTime(selectedSubmittedRequest.overtime_end_at) }}</div>
            <div><span class="font-medium text-gray-900">Phut de nghi:</span> {{ formatMinutes(selectedSubmittedRequest.requested_minutes) }}</div>
            <div><span class="font-medium text-gray-900">Phut duyet:</span> {{ formatMinutes(selectedSubmittedRequest.approved_minutes) }}</div>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700" @click="closeSubmittedRequestDetail">
            Dong
          </button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'
import InputDate from '@/components/forms/InputDate.vue'
import Modal from '@/components/ui/Modal.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  records: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
  request_types: { type: Array, default: () => [] },
  recent_requests: { type: Array, default: () => [] },
  overtime_catalog: { type: Object, default: () => ({}) },
  leave_types: { type: Array, default: () => [] },
  leave_balances: { type: Array, default: () => [] },
})

const page = usePage()
const attachmentInput = ref(null)
const selectedSubmittedRequest = ref(null)

const filterForm = reactive({
  month: Number(props.filters.month),
  year: Number(props.filters.year),
  period: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
})

let lastAppliedPeriod = `${filterForm.year}-${filterForm.month}`

const requestForm = useForm({
  request_type: '',
  request_date: '',
  from_date: '',
  to_date: '',
  from_time: '',
  to_time: '',
  leave_type_id: '',
  leave_type: 'paid',
  leave_duration_type: 'full_day',
  leave_hours: '',
  attachment: null,
  requested_status: 'late',
  start_at: '',
  end_at: '',
  reason: '',
})

const columns = [
  { label: 'Ngay cong', key: 'work_date' },
  { label: 'Check in', key: 'check_in_at' },
  { label: 'Check out', key: 'check_out_at' },
  { label: 'Gio lam', key: 'worked_minutes', align: 'text-center' },
  { label: 'Di muon', key: 'late_minutes', align: 'text-center' },
  { label: 'Ve som', key: 'early_leave_minutes', align: 'text-center' },
  { label: 'Tang ca', key: 'overtime_minutes', align: 'text-center' },
  { label: 'Ket qua cong', key: 'attendance_status', align: 'text-center' },
  { label: 'Trang thai ngay', key: 'day_status', align: 'text-center' },
  { label: 'Duyet', key: 'approval_status', align: 'text-center' },
  { label: 'Don lien quan', key: 'request_presence_label', align: 'text-center' },
]

const requestColumns = [
  { label: 'Loai don', key: 'request_type_label' },
  { label: 'Ngay ap dung', key: 'request_date' },
  { label: 'Khoang thoi gian', key: 'period' },
  { label: 'Trang thai', key: 'status_label', align: 'text-center' },
  { label: 'Nguoi duyet', key: 'reviewed_by_name' },
  { label: 'Ghi chu duyet', key: 'review_note' },
  { label: 'Gui luc', key: 'submitted_at' },
]

const requestActions = [
  {
    label: 'Chi tiet',
    buttonProps: {
      title: 'Xem chi tiet don da gui',
      class: 'border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm hover:border-blue-300 hover:bg-blue-100 hover:text-blue-800',
    },
    onClick: (item) => openSubmittedRequestDetail(item),
  },
]

const summaryCards = computed(() => [
  { label: 'Ngay cong hop le', value: formatWorkUnits(props.summary.approved_work_units ?? props.summary.total_work_units ?? 0) },
  { label: 'Ngay can xu ly', value: props.summary.action_required_records ?? props.summary.pending_records ?? 0 },
  { label: 'Don cho duyet', value: props.summary.pending_request_records ?? 0 },
  { label: 'Gio lam da duyet', value: formatMinutes(props.summary.approved_worked_minutes ?? props.summary.total_worked_minutes ?? 0) },
])

const currentPeriodLabel = computed(() => {
  const date = parsePeriodValue(filterForm.period)
  if (!date) return `Thang ${filterForm.month} / ${filterForm.year}`

  return new Intl.DateTimeFormat('vi-VN', {
    month: 'long',
    year: 'numeric',
  }).format(date)
})

const formError = computed(() => requestForm.errors.error || page.props.errors?.error || '')
const overtimeCatalog = computed(() => props.overtime_catalog || {})
const selectedLeaveType = computed(() => (props.leave_types || []).find((type) => Number(type.id) === Number(requestForm.leave_type_id)) || null)
const selectedLeaveBalance = computed(() => (props.leave_balances || []).find((balance) => Number(balance.leave_type_id) === Number(requestForm.leave_type_id)) || null)
const selectedLeaveAvailableDays = computed(() => selectedLeaveBalance.value?.available_days ?? selectedLeaveType.value?.annual_quota ?? 0)
const showLeaveAttachmentField = computed(() => requestForm.request_type === 'leave' && Boolean(selectedLeaveType.value?.requires_attachment))
const overtimeWindowLabel = computed(() => {
  if (!overtimeCatalog.value.start_time || !overtimeCatalog.value.end_time) return 'Chua cau hinh'
  return `${overtimeCatalog.value.start_time} - ${overtimeCatalog.value.end_time}`
})
const showSingleDate = computed(() => ['forgot_check', 'late_early', 'make_up'].includes(requestForm.request_type))
const showDateRange = computed(() => ['leave', 'business_trip'].includes(requestForm.request_type))
const showTimeRange = computed(() => ['forgot_check', 'late_early', 'make_up'].includes(requestForm.request_type))
const singleDateLabel = computed(() => requestForm.request_type === 'forgot_check' ? 'Ngay quen cham cong' : 'Ngay ap dung')
const fromTimeLabel = computed(() => {
  if (requestForm.request_type === 'make_up') return 'Bat dau lam bu'
  if (requestForm.request_type === 'forgot_check') return 'Gio check-in neu quen'
  return 'Tu gio'
})
const toTimeLabel = computed(() => {
  if (requestForm.request_type === 'make_up') return 'Ket thuc lam bu'
  if (requestForm.request_type === 'forgot_check') return 'Gio check-out neu quen'
  return 'Den gio'
})

const datePickerConfig = {
  allowInput: false,
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
}

const periodPickerConfig = {
  allowInput: false,
  dateFormat: 'Y-m-01',
  altInput: true,
  altFormat: 'm/Y',
  defaultDate: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
}

const timePickerConfig = {
  allowInput: false,
  enableTime: true,
  noCalendar: true,
  time_24hr: true,
  minuteIncrement: 5,
  dateFormat: 'H:i',
  altInput: true,
  altFormat: 'H:i',
}

const dateTimePickerConfig = {
  allowInput: false,
  enableTime: true,
  time_24hr: true,
  minuteIncrement: 5,
  dateFormat: 'Y-m-d H:i',
  altInput: true,
  altFormat: 'd/m/Y H:i',
}

const REQUEST_TYPE_LABELS = {
  leave: 'Xin nghi phep',
  late_early: 'Xin di muon / ve som',
  forgot_check: 'Xin quen cham cong',
  business_trip: 'Xin cong tac',
  make_up: 'Xin lam bu',
  overtime: 'Dang ky tang ca',
}

watch(() => requestForm.request_type, (type) => {
  requestForm.clearErrors()
  requestForm.request_date = ''
  requestForm.from_date = ''
  requestForm.to_date = ''
  requestForm.from_time = ''
  requestForm.to_time = ''
  requestForm.leave_type_id = ''
  requestForm.leave_duration_type = 'full_day'
  requestForm.leave_hours = ''
  requestForm.attachment = null
  if (attachmentInput.value) {
    attachmentInput.value.value = ''
  }
  requestForm.start_at = ''
  requestForm.end_at = ''

  if (type !== 'leave') {
    requestForm.leave_type = 'paid'
  }

  if (type !== 'late_early') {
    requestForm.requested_status = 'late'
  }

  if (type === 'overtime') {
    requestForm.request_date = overtimeCatalog.value.work_date || new Date().toISOString().slice(0, 10)
  }
})

watch(() => requestForm.leave_type_id, () => {
  requestForm.attachment = null
  if (attachmentInput.value) {
    attachmentInput.value.value = ''
  }
})

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

function shiftPeriod(direction) {
  const currentMonth = Number(filterForm.month)
  const currentYear = Number(filterForm.year)

  if (!currentMonth || !currentYear) return

  const period = new Date(currentYear, currentMonth - 1 + direction, 1)
  filterForm.month = period.getMonth() + 1
  filterForm.year = period.getFullYear()
}

function jumpToCurrentPeriod() {
  const now = new Date()
  filterForm.month = now.getMonth() + 1
  filterForm.year = now.getFullYear()
  filterForm.period = buildPeriodValue(filterForm.year, filterForm.month)
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

function submitAttendanceRequest() {
  if (requestForm.request_type === 'leave' && selectedLeaveType.value) {
    requestForm.leave_type = selectedLeaveType.value.is_paid ? 'paid' : 'unpaid'
  }

  requestForm.post(route('attendance.requests.store'), {
    preserveScroll: true,
    onSuccess: () => requestForm.reset(),
  })
}

function setAttachment(event) {
  requestForm.attachment = event.target.files?.[0] || null
}

function openSubmittedRequestDetail(item) {
  selectedSubmittedRequest.value = item
}

function closeSubmittedRequestDetail() {
  selectedSubmittedRequest.value = null
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
  if (minutes <= 0) return '0 phut'
  const hours = Math.floor(minutes / 60)
  const remainMinutes = minutes % 60
  if (hours <= 0) return `${remainMinutes} phut`
  if (remainMinutes === 0) return `${hours} gio`
  return `${hours} gio ${remainMinutes} phut`
}

function formatWorkUnits(value) {
  const units = Number(value) || 0
  return Number.isInteger(units) ? `${units}` : units.toFixed(1)
}

function leaveDurationLabel(item) {
  if (!item?.leave_duration_type) return '-'
  if (item.leave_duration_type === 'full_day') return 'Ca ngay'
  if (item.leave_duration_type === 'half_day') return 'Nua ngay'
  if (item.leave_duration_type === 'hourly') {
    return `Theo gio${item.leave_hours ? ` (${formatWorkUnits(item.leave_hours)} gio)` : ''}`
  }

  return item.leave_duration_type
}

function hasMissingCheck(item) {
  return item?.day_status === 'missing_check_in' || item?.day_status === 'missing_check_out' || item?.missing_check_in || item?.missing_check_out
}

function shouldShowViolationMinutes(item) {
  return item?.approval_status !== 'rejected' && !hasMissingCheck(item)
}

function formatAttendanceStatus(item) {
  const resolvedUnit = Number(item?.work_unit)

  if (hasMissingCheck(item)) {
    return 'Chua tinh cong'
  }

  if (item?.approval_status === 'rejected') {
    return 'Khong duyet cong'
  }

  if (resolvedUnit === 1) {
    return 'Du cong'
  }

  if (resolvedUnit === 0.5) {
    return 'Nua cong'
  }

  if (!Number.isNaN(resolvedUnit) && resolvedUnit === 0) {
    return 'Khong cong'
  }

  const labels = {
    on_time: 'Dung gio',
    late: 'Tre',
    absent: 'Vang',
  }
  return labels[item?.attendance_status] || '-'
}

function formatDayStatus(value) {
  const labels = {
    present: 'Di lam',
    late: 'Di muon',
    early_leave: 'Ve som',
    leave: 'Nghi phep',
    unpaid_leave: 'Nghi khong phep',
    business_trip: 'Cong tac',
    missing_check_in: 'Thieu check in',
    missing_check_out: 'Thieu check out',
    absent: 'Vang',
  }
  return labels[value] || '-'
}

function formatApprovalStatus(value) {
  const labels = {
    pending: 'Cho duyet',
    approved: 'Da duyet',
    rejected: 'Tu choi',
  }
  return labels[value] || '-'
}

function statusClass(item) {
  const resolvedUnit = Number(item?.work_unit)

  if (hasMissingCheck(item)) {
    return 'bg-yellow-50 text-yellow-700'
  }

  if (item?.approval_status === 'rejected') {
    return 'bg-rose-50 text-rose-700'
  }

  if (resolvedUnit === 1) {
    return 'bg-emerald-50 text-emerald-700'
  }

  if (resolvedUnit === 0.5) {
    return 'bg-blue-50 text-blue-700'
  }

  if (!Number.isNaN(resolvedUnit) && resolvedUnit === 0) {
    return 'bg-orange-50 text-orange-700'
  }

  return item?.attendance_status === 'late'
    ? 'bg-amber-50 text-amber-700'
    : item?.attendance_status === 'absent'
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

function requestPresenceClass(item) {
  if (item?.request_status === 'approved') return 'bg-emerald-50 text-emerald-700'
  if (item?.request_status === 'pending') return 'bg-amber-50 text-amber-700'
  if (item?.request_status === 'rejected') return 'bg-rose-50 text-rose-700'
  return 'bg-slate-50 text-slate-700'
}
</script>
