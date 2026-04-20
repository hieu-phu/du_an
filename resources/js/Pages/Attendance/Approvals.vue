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

    <div class="mb-6 rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <div class="text-sm font-semibold text-gray-900">Bo loc ky duyet cong</div>
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
            placeholder="Tat ca"
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
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bản ghi chấm công chờ duyệt</h3>
      </div>

      <DataTable :columns="columns" :data="paginatedRecords" :actions="actions" :show-index="true" :index-offset="pageOffset('records')" empty-message="Không có bản ghi chờ duyệt.">
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
        <template #cell-request_presence="{ item }">
          <span :class="requestPresenceClass(item.request_presence)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ item.request_presence_label || 'Khong co don' }}
          </span>
        </template>
      </DataTable>
      <LocalPagination
        v-if="records.length"
        label="ban ghi"
        :total="records.length"
        :page="pagination.records"
        :per-page="perPage.records"
        :options="perPageOptions"
        @page="setPage('records', $event)"
        @per-page="setPerPage('records', $event)"
      />
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Đơn chấm công chờ duyệt</h3>
      </div>

      <DataTable :columns="requestColumns" :data="paginatedRequestApprovals" :actions="requestActions" :show-index="true" :index-offset="pageOffset('requests')" empty-message="Không có đơn chấm công chờ duyệt.">
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
      <LocalPagination
        v-if="request_approvals.length"
        label="don"
        :total="request_approvals.length"
        :page="pagination.requests"
        :per-page="perPage.requests"
        :options="perPageOptions"
        @page="setPage('requests', $event)"
        @per-page="setPerPage('requests', $event)"
      />
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Lich su duyet cong</h3>
      </div>

      <DataTable :columns="reviewedRecordColumns" :data="paginatedReviewedRecords" :show-index="true" :index-offset="pageOffset('reviewedRecords')" empty-message="Chua co lich su duyet cong trong ky.">
        <template #cell-work_date="{ item }">
          {{ formatDate(item.work_date) }}
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
        <template #cell-reviewed_at="{ item }">
          {{ formatDateTime(item.reviewed_at) }}
        </template>
      </DataTable>
      <LocalPagination
        v-if="request_approvals.length"
        label="don"
        :total="request_approvals.length"
        :page="pagination.requests"
        :per-page="perPage.requests"
        :options="perPageOptions"
        @page="setPage('requests', $event)"
        @per-page="setPerPage('requests', $event)"
      />
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Lich su duyet don cham cong</h3>
      </div>

      <DataTable :columns="reviewedRequestColumns" :data="paginatedReviewedRequestApprovals" :show-index="true" :index-offset="pageOffset('reviewedRequests')" empty-message="Chua co lich su duyet don trong ky.">
        <template #cell-request_date="{ item }">
          {{ formatDate(item.request_date) }}
        </template>
        <template #cell-submitted_at="{ item }">
          {{ formatDateTime(item.submitted_at) }}
        </template>
        <template #cell-reviewed_at="{ item }">
          {{ formatDateTime(item.reviewed_at) }}
        </template>
        <template #cell-status_label="{ item }">
          <span :class="approvalStatusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ item.status_label }}
          </span>
        </template>
      </DataTable>
      <LocalPagination
        v-if="reviewed_records.length"
        label="ban ghi"
        :total="reviewed_records.length"
        :page="pagination.reviewedRecords"
        :per-page="perPage.reviewedRecords"
        :options="perPageOptions"
        @page="setPage('reviewedRecords', $event)"
        @per-page="setPerPage('reviewedRecords', $event)"
      />
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
import { computed, h, reactive, ref, watch } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'
import Modal from '@/components/ui/Modal.vue'
import InputDate from '@/components/forms/InputDate.vue'
import FormSelect from '@/components/forms/FormSelect.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  records: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
  employees: { type: Array, default: () => [] },
  request_approvals: { type: Array, default: () => [] },
  reviewed_records: { type: Array, default: () => [] },
  reviewed_request_approvals: { type: Array, default: () => [] },
  approval_summary: { type: Object, default: () => ({}) },
})

const page = usePage()
const currentAuthorityLevel = computed(() => Number(page.props.auth?.user?.authority_level || 0))
const selectedRequest = ref(null)
const decisionForm = useForm({ note: '' })
const requestDecisionForm = useForm({ note: '' })
const filterForm = reactive({
  month: Number(props.filters.month),
  year: Number(props.filters.year),
  period: buildPeriodValue(Number(props.filters.year), Number(props.filters.month)),
  employee_profile_id: props.filters.employee_profile_id ?? null,
})

let lastAppliedFilterKey = `${filterForm.year}-${filterForm.month}-${filterForm.employee_profile_id ?? 'all'}`

const perPageOptions = [10, 20, 50]
const pagination = reactive({
  records: 1,
  requests: 1,
  reviewedRecords: 1,
  reviewedRequests: 1,
})
const perPage = reactive({
  records: 10,
  requests: 10,
  reviewedRecords: 10,
  reviewedRequests: 10,
})

const LocalPagination = {
  name: 'LocalPagination',
  props: {
    total: { type: Number, required: true },
    page: { type: Number, required: true },
    perPage: { type: Number, required: true },
    options: { type: Array, default: () => [10, 20, 50] },
    label: { type: String, default: 'ban ghi' },
  },
  emits: ['page', 'per-page'],
  setup(componentProps, { emit }) {
    const totalPages = computed(() => Math.max(1, Math.ceil(componentProps.total / componentProps.perPage)))
    const from = computed(() => componentProps.total ? ((componentProps.page - 1) * componentProps.perPage) + 1 : 0)
    const to = computed(() => Math.min(componentProps.total, componentProps.page * componentProps.perPage))
    const visiblePages = computed(() => {
      const current = componentProps.page
      const last = totalPages.value
      const start = Math.max(1, current - 2)
      const end = Math.min(last, current + 2)

      return Array.from({ length: end - start + 1 }, (_, index) => start + index)
    })
    const goToPage = (page) => emit('page', Math.min(Math.max(1, page), totalPages.value))
    const buttonClass = (active = false, disabled = false) => [
      'rounded-lg border px-3 py-2 text-sm font-semibold transition',
      active ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:text-blue-700',
      disabled ? 'cursor-not-allowed opacity-50 hover:border-gray-200 hover:text-gray-700' : '',
    ].join(' ')

    return () => h('div', { class: 'mt-4 flex flex-col gap-3 border-t border-gray-100 pt-4 md:flex-row md:items-center md:justify-between' }, [
      h('div', { class: 'text-sm font-medium text-gray-600' }, `Hien thi ${from.value}-${to.value} / ${componentProps.total} ${componentProps.label}`),
      h('div', { class: 'flex flex-wrap items-center gap-2' }, [
        h('label', { class: 'text-sm text-gray-600' }, 'Moi trang'),
        h('select', {
          class: 'rounded-lg border border-gray-300 px-3 py-2 text-sm',
          value: componentProps.perPage,
          onChange: (event) => emit('per-page', Number(event.target.value)),
        }, componentProps.options.map((option) => h('option', { value: option }, option))),
        h('button', {
          type: 'button',
          class: buttonClass(false, componentProps.page <= 1),
          disabled: componentProps.page <= 1,
          onClick: () => goToPage(componentProps.page - 1),
        }, 'Truoc'),
        ...visiblePages.value.map((page) => h('button', {
          type: 'button',
          class: buttonClass(page === componentProps.page),
          onClick: () => goToPage(page),
        }, String(page))),
        h('button', {
          type: 'button',
          class: buttonClass(false, componentProps.page >= totalPages.value),
          disabled: componentProps.page >= totalPages.value,
          onClick: () => goToPage(componentProps.page + 1),
        }, 'Sau'),
      ]),
    ])
  },
}

const paginatedRecords = computed(() => paginateItems(props.records, 'records'))
const paginatedRequestApprovals = computed(() => paginateItems(props.request_approvals, 'requests'))
const paginatedReviewedRecords = computed(() => paginateItems(props.reviewed_records, 'reviewedRecords'))
const paginatedReviewedRequestApprovals = computed(() => paginateItems(props.reviewed_request_approvals, 'reviewedRequests'))

function paginateItems(items, key) {
  const page = pagination[key] || 1
  const limit = perPage[key] || 10
  const start = (page - 1) * limit

  return (items || []).slice(start, start + limit)
}

function pageOffset(key) {
  return ((pagination[key] || 1) - 1) * (perPage[key] || 10)
}

function setPage(key, page) {
  pagination[key] = Math.min(Math.max(1, Number(page) || 1), totalPagesFor(key))
}

function setPerPage(key, value) {
  perPage[key] = Number(value) || 10
  pagination[key] = 1
}

function totalPagesFor(key) {
  const totals = {
    records: props.records.length,
    requests: props.request_approvals.length,
    reviewedRecords: props.reviewed_records.length,
    reviewedRequests: props.reviewed_request_approvals.length,
  }

  return Math.max(1, Math.ceil((totals[key] || 0) / (perPage[key] || 10)))
}

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
  { label: 'Don', key: 'request_presence', align: 'text-center' },
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

const reviewedRecordColumns = [
  { label: 'Nhan vien', key: 'employee_name' },
  { label: 'Ma NV', key: 'employee_code' },
  { label: 'Ngay cong', key: 'work_date' },
  { label: 'Gio lam', key: 'worked_minutes', align: 'text-center' },
  { label: 'Di muon', key: 'late_minutes', align: 'text-center' },
  { label: 'Ve som', key: 'early_leave_minutes', align: 'text-center' },
  { label: 'Trang thai ngay', key: 'day_status', align: 'text-center' },
  { label: 'Ket qua duyet', key: 'approval_status', align: 'text-center' },
  { label: 'Nguoi duyet', key: 'reviewed_by_name' },
  { label: 'Duyet luc', key: 'reviewed_at' },
  { label: 'Ghi chu', key: 'approval_note' },
]

const reviewedRequestColumns = [
  { label: 'Nhan vien', key: 'employee_name' },
  { label: 'Ma NV', key: 'employee_code' },
  { label: 'Loai don', key: 'request_type_label' },
  { label: 'Ngay ap dung', key: 'request_date' },
  { label: 'Khoang thoi gian', key: 'period' },
  { label: 'Ket qua duyet', key: 'status_label', align: 'text-center' },
  { label: 'Nguoi duyet', key: 'reviewed_by_name' },
  { label: 'Duyet luc', key: 'reviewed_at' },
  { label: 'Ghi chu', key: 'review_note' },
]

const summaryCards = computed(() => [
  { label: 'Công chờ duyệt', value: props.approval_summary.pending_records ?? props.summary.pending_records ?? 0 },
  { label: 'Đã duyệt', value: props.approval_summary.approved_records ?? props.summary.confirmed_records ?? 0 },
  { label: 'Từ chối', value: props.approval_summary.rejected_records ?? props.summary.rejected_records ?? 0 },
  { label: 'Đơn chờ duyệt', value: props.request_approvals.length },
])

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
  { value: null, label: 'Tat ca' },
  ...(props.employees || []).map((employee) => ({
    value: employee.id,
    label: employee.label,
  })),
])

const actions = [
  {
    label: 'Duyệt',
    buttonProps: { title: 'Duyệt ngày công' },
    hidden: (item) => item.approval_status !== 'pending' || (Number(item.employee_authority_level || 0) >= currentAuthorityLevel.value),
    onClick: (item) => decide(item, 'approve'),
  },
  {
    label: 'Từ chối',
    buttonProps: { title: 'Từ chối ngày công' },
    hidden: (item) => item.approval_status !== 'pending' || (Number(item.employee_authority_level || 0) >= currentAuthorityLevel.value),
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
    applyFilters()
  }
)

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

function jumpToCurrentPeriod() {
  const now = new Date()
  filterForm.month = now.getMonth() + 1
  filterForm.year = now.getFullYear()
  filterForm.period = buildPeriodValue(filterForm.year, filterForm.month)
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

function requestPresenceClass(value) {
  const classes = {
    has_request: 'bg-emerald-50 text-emerald-700',
    no_request: 'bg-rose-50 text-rose-700',
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


