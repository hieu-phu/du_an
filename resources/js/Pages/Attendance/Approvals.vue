<template>
  <Head :title="pageTitle" />

  <AdminLayout>
    <PageBreadcrumb :title="pageTitle" :items="breadcrumbItems" />

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div v-for="card in summaryCards" :key="card.label" class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm">
        <div class="text-sm text-gray-500">{{ card.label }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
      </div>
    </div>

    <div class="mb-6 rounded-[24px] border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <div class="text-sm font-semibold text-gray-900">{{ filterTitle }}</div>
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

    <div v-if="!isLeaveApproval" class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bản ghi chấm công chờ duyệt</h3>
      </div>

      <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
          <p class="text-sm text-gray-500">Ca lam va gio chuan lay tu phan ca/snapshot cua ngay cong.</p>
          <p v-if="bulkBlockedRecordsOnPage.length" class="text-xs font-medium text-amber-700">
            {{ bulkBlockedRecordsOnPage.length }} ban ghi tren trang nay khong the chon hang loat vi thieu check-out hoac khong du quyen.
          </p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
          <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700">
            <input
              type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              :checked="isCurrentPageSelected"
              :disabled="!bulkApprovableRecordsOnPage.length"
              @change="toggleCurrentPageSelection"
            />
            Chon trang hien tai
          </label>
          <button
            type="button"
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!selectedRecordIds.length || bulkDecisionForm.processing"
            @click="approveSelectedRecords"
          >
            Duyet hang loat ({{ selectedRecordIds.length }})
          </button>
        </div>
      </div>

      <DataTable :columns="columns" :data="paginatedRecords" :actions="actions" :show-index="true" :index-offset="pageOffset('records')" empty-message="Không có bản ghi chờ duyệt.">
        <template #head-selection>
          <input
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            :checked="isCurrentPageSelected"
            :disabled="!bulkApprovableRecordsOnPage.length"
            title="Chon tat ca ban ghi co the duyet tren trang nay"
            @change="toggleCurrentPageSelection"
          />
        </template>
        <template #cell-selection="{ item }">
          <div class="flex min-w-[80px] flex-col items-center justify-center gap-1">
            <input
              type="checkbox"
              class="h-4 w-4 shrink-0 rounded border-gray-300 text-blue-600 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
              :checked="selectedRecordIds.includes(item.id)"
              :disabled="!isRecordBulkApprovable(item)"
              :title="bulkSelectionTitle(item)"
              @change="toggleRecordSelection(item)"
              @click.stop
            />
            <span
              v-if="bulkSelectionBlockReason(item)"
              class="max-w-[78px] rounded-full bg-amber-50 px-2 py-1 text-center text-[10px] font-semibold leading-3 text-amber-700"
              :title="bulkSelectionBlockReason(item)"
            >
              {{ bulkSelectionShortReason(item) }}
            </span>
          </div>
        </template>
        <template #cell-work_date="{ item }">
          {{ formatDate(item.work_date) }}
        </template>
        <template #cell-shift_info="{ item }">
          <div class="min-w-[140px]">
            <div class="font-semibold text-gray-900">{{ item.shift_name || 'Ca mac dinh' }}</div>
            <div class="text-xs text-gray-500">{{ formatTimeRange(item.shift_start_time, item.shift_end_time) }}</div>
          </div>
        </template>
        <template #cell-standard_minutes="{ item }">
          <div class="text-center">
            <div class="font-semibold text-gray-900">{{ formatMinutes(item.standard_minutes) }}</div>
            <div v-if="item.break_minutes || item.handover_break_minutes" class="text-[11px] text-gray-500">
              Nghi {{ formatMinutes(item.break_minutes) }}<span v-if="item.handover_break_minutes">, giao ca {{ formatMinutes(item.handover_break_minutes) }}</span>
            </div>
          </div>
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
          <div class="flex flex-wrap justify-center gap-1.5">
            <span
              v-for="badge in dayStatusBadges(item)"
              :key="badge.label"
              :class="badge.class"
              class="rounded-full px-3 py-1 text-xs font-semibold"
            >
              {{ badge.label }}
            </span>
          </div>
        </template>
        <template #cell-approval_status="{ item }">
          <span :class="approvalStatusClass(item.display_approval_status || item.approval_status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatApprovalStatus(item.display_approval_status || item.approval_status) }}
          </span>
        </template>
        <template #cell-request_presence="{ item }">
          <button
            v-if="hasRequestDetail(item)"
            type="button"
            :class="requestPresenceClass(item.request_presence)"
            :title="item.request_reason || item.request_presence_label || 'Khong co don'"
            class="inline-flex max-w-[170px] min-w-[118px] justify-center rounded-full px-3 py-1 text-xs font-semibold transition hover:opacity-80"
            @click="openRequestDetailFromRecord(item)"
          >
            <span class="truncate">{{ item.request_presence_label || 'Khong co don' }}</span>
          </button>
          <span
            v-else
            :class="requestPresenceClass(item.request_presence)"
            :title="item.request_reason || item.request_presence_label || 'Khong co don'"
            class="inline-flex max-w-[170px] min-w-[118px] justify-center rounded-full px-3 py-1 text-xs font-semibold"
          >
            <span class="truncate">{{ item.request_presence_label || 'Khong co don' }}</span>
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
        <h3 class="text-lg font-semibold text-gray-900">{{ pendingRequestTitle }}</h3>
      </div>

      <div v-if="isLeaveApproval" class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-500">
          Chon nhieu don nghi phep cho duyet tren trang hien tai neu can.
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
          <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700">
            <input
              type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              :checked="isCurrentRequestPageSelected"
              :disabled="!bulkApprovableRequestsOnPage.length"
              @change="toggleCurrentRequestPageSelection"
            />
            Chon trang hien tai
          </label>
          <button
            type="button"
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!selectedApprovalRequestIds.length || requestBulkDecisionForm.processing"
            @click="approveSelectedRequests"
          >
            Duyet hang loat ({{ selectedApprovalRequestIds.length }})
          </button>
        </div>
      </div>

      <DataTable :columns="displayRequestColumns" :data="paginatedRequestApprovals" :actions="requestActions" :show-index="true" :index-offset="pageOffset('requests')" :empty-message="pendingRequestEmptyMessage">
        <template v-if="isLeaveApproval" #head-selection>
          <input
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            :checked="isCurrentRequestPageSelected"
            :disabled="!bulkApprovableRequestsOnPage.length"
            title="Chon tat ca don co the duyet tren trang nay"
            @change="toggleCurrentRequestPageSelection"
          />
        </template>
        <template v-if="isLeaveApproval" #cell-selection="{ item }">
          <div class="flex min-w-[52px] items-center justify-center">
            <input
              type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
              :checked="selectedApprovalRequestIds.includes(item.id)"
              :disabled="!isRequestBulkApprovable(item)"
              title="Chon de duyet hang loat"
              @change="toggleRequestSelection(item)"
              @click.stop
            />
          </div>
        </template>
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

    <div v-if="!isLeaveApproval" class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4 flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Lich su duyet cong</h3>
      </div>

      <DataTable :columns="reviewedRecordColumns" :data="paginatedReviewedRecords" :show-index="true" :index-offset="pageOffset('reviewedRecords')" empty-message="Chua co lich su duyet cong trong ky.">
        <template #cell-work_date="{ item }">
          {{ formatDate(item.work_date) }}
        </template>
        <template #cell-shift_info="{ item }">
          <div class="min-w-[140px]">
            <div class="font-semibold text-gray-900">{{ item.shift_name || 'Ca mac dinh' }}</div>
            <div class="text-xs text-gray-500">{{ formatTimeRange(item.shift_start_time, item.shift_end_time) }}</div>
          </div>
        </template>
        <template #cell-worked_minutes="{ item }">
          {{ formatMinutes(item.worked_minutes) }}
        </template>
        <template #cell-standard_minutes="{ item }">
          <div class="text-center">
            <div class="font-semibold text-gray-900">{{ formatMinutes(item.standard_minutes) }}</div>
            <div v-if="item.break_minutes || item.handover_break_minutes" class="text-[11px] text-gray-500">
              Nghi {{ formatMinutes(item.break_minutes) }}<span v-if="item.handover_break_minutes">, giao ca {{ formatMinutes(item.handover_break_minutes) }}</span>
            </div>
          </div>
        </template>
        <template #cell-late_minutes="{ item }">
          {{ formatMinutes(item.late_minutes) }}
        </template>
        <template #cell-early_leave_minutes="{ item }">
          {{ formatMinutes(item.early_leave_minutes) }}
        </template>
        <template #cell-day_status="{ item }">
          <div class="flex flex-wrap justify-center gap-1.5">
            <span
              v-for="badge in dayStatusBadges(item)"
              :key="badge.label"
              :class="badge.class"
              class="rounded-full px-3 py-1 text-xs font-semibold"
            >
              {{ badge.label }}
            </span>
          </div>
        </template>
        <template #cell-approval_status="{ item }">
          <span :class="approvalStatusClass(item.display_approval_status || item.approval_status)" class="rounded-full px-3 py-1 text-xs font-semibold">
            {{ formatApprovalStatus(item.display_approval_status || item.approval_status) }}
          </span>
        </template>
        <template #cell-reviewed_at="{ item }">
          {{ formatDateTime(item.reviewed_at) }}
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

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ reviewedRequestTitle }}</h3>
      </div>

      <DataTable :columns="reviewedRequestColumns" :data="paginatedReviewedRequestApprovals" :actions="requestActions" :show-index="true" :index-offset="pageOffset('reviewedRequests')" :empty-message="reviewedRequestEmptyMessage">
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
        v-if="reviewed_request_approvals.length"
        label="don"
        :total="reviewed_request_approvals.length"
        :page="pagination.reviewedRequests"
        :per-page="perPage.reviewedRequests"
        :options="perPageOptions"
        @page="setPage('reviewedRequests', $event)"
        @per-page="setPerPage('reviewedRequests', $event)"
      />
    </div>

    <Modal :show="!!selectedRequest" @close="closeRequestDetail">
      <div v-if="selectedRequest" class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-900">{{ requestDetailTitle }}</h3>
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
          <div class="mb-2 font-semibold text-gray-900">{{ requestSupplementTitle }}</div>
          <div><span class="font-medium text-gray-900">Từ ngày:</span> {{ formatDate(selectedRequest.from_date) }}</div>
          <div><span class="font-medium text-gray-900">Đến ngày:</span> {{ formatDate(selectedRequest.to_date) }}</div>
          <div><span class="font-medium text-gray-900">Từ giờ:</span> {{ selectedRequest.from_time || '-' }}</div>
          <div><span class="font-medium text-gray-900">Đến giờ:</span> {{ selectedRequest.to_time || '-' }}</div>
          <div><span class="font-medium text-gray-900">Loại nghỉ:</span> {{ selectedRequest.leave_type_name || selectedRequest.leave_type || '-' }}</div>
          <div><span class="font-medium text-gray-900">Trạng thái đề nghị:</span> {{ selectedRequest.requested_status || '-' }}</div>
          <div v-if="selectedRequest.business_trip_location"><span class="font-medium text-gray-900">Địa điểm công tác:</span> {{ selectedRequest.business_trip_location }}</div>
          <div v-if="selectedRequest.make_up_related_leave_date"><span class="font-medium text-gray-900">Ngày nghỉ liên kết:</span> {{ formatDate(selectedRequest.make_up_related_leave_date) }}</div>
          <div v-if="selectedRequest.request_type === 'leave'" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
            <div class="font-medium text-amber-900">Minh chứng</div>
            <div v-if="selectedRequest.attachment_url" class="mt-1">
              <a
                :href="selectedRequest.attachment_url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-semibold text-blue-700 underline underline-offset-2"
              >
                Xem tệp đính kèm
              </a>
            </div>
            <div v-else-if="selectedRequest.leave_type_requires_attachment" class="mt-1 text-sm text-rose-700">
              Loại nghỉ này yêu cầu minh chứng nhưng đơn hiện không có tệp đính kèm.
            </div>
            <div v-else class="mt-1 text-sm text-gray-500">
              Loại nghỉ này không yêu cầu minh chứng.
            </div>
          </div>
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
  approval_mode: { type: String, default: 'attendance' },
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
const isLeaveApproval = computed(() => props.approval_mode === 'leave')
const pageTitle = computed(() => isLeaveApproval.value ? 'Duyệt nghỉ phép' : 'Duyệt chấm công')
const breadcrumbItems = computed(() => isLeaveApproval.value
  ? [{ text: 'Nghỉ phép', link: null }, { text: 'Duyệt nghỉ phép', link: null }]
  : [{ text: 'Chấm công', link: null }, { text: 'Duyệt công', link: null }]
)
const filterTitle = computed(() => isLeaveApproval.value ? 'Bo loc ky duyet nghi phep' : 'Bo loc ky duyet cong')
const pendingRequestTitle = computed(() => isLeaveApproval.value ? 'Đơn nghỉ phép chờ duyệt' : 'Đơn chấm công chờ duyệt')
const pendingRequestEmptyMessage = computed(() => isLeaveApproval.value ? 'Không có đơn nghỉ phép chờ duyệt.' : 'Không có đơn chấm công chờ duyệt.')
const reviewedRequestTitle = computed(() => isLeaveApproval.value ? 'Lich su duyet nghi phep' : 'Lich su duyet don cham cong')
const reviewedRequestEmptyMessage = computed(() => isLeaveApproval.value ? 'Chua co lich su duyet nghi phep trong ky.' : 'Chua co lich su duyet don trong ky.')
const requestDetailTitle = computed(() => isLeaveApproval.value ? 'Chi tiết đơn nghỉ phép' : 'Chi tiết đơn chấm công')
const requestSupplementTitle = computed(() => isLeaveApproval.value ? 'Thông tin nghỉ phép' : 'Thông tin bổ sung đơn chấm công')
const currentAuthorityLevel = computed(() => Number(page.props.auth?.user?.authority_level || 0))
const selectedRequest = ref(null)
const decisionForm = useForm({
  note: '',
  resolved_check_out_time: '',
})
const bulkDecisionForm = useForm({
  record_ids: [],
  note: '',
})
const requestDecisionForm = useForm({ note: '' })
const requestBulkDecisionForm = useForm({
  approval_request_ids: [],
  note: '',
})
const selectedRecordIds = ref([])
const selectedApprovalRequestIds = ref([])
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
const bulkApprovableRecordsOnPage = computed(() => paginatedRecords.value.filter((item) => isRecordBulkApprovable(item)))
const bulkBlockedRecordsOnPage = computed(() => paginatedRecords.value.filter((item) => item.approval_status === 'pending' && !isRecordBulkApprovable(item)))
const bulkApprovableRequestsOnPage = computed(() => paginatedRequestApprovals.value.filter((item) => isRequestBulkApprovable(item)))
const isCurrentPageSelected = computed(() => {
  const ids = bulkApprovableRecordsOnPage.value.map((item) => item.id)

  return ids.length > 0 && ids.every((id) => selectedRecordIds.value.includes(id))
})
const isCurrentRequestPageSelected = computed(() => {
  const ids = bulkApprovableRequestsOnPage.value.map((item) => item.id)

  return ids.length > 0 && ids.every((id) => selectedApprovalRequestIds.value.includes(id))
})

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
  { label: '', key: 'selection', align: 'text-center', class: 'w-[92px] min-w-[92px]' },
  { label: 'Nhân viên', key: 'employee_name', class: 'min-w-[120px]' },
  { label: 'Mã NV', key: 'employee_code', class: 'min-w-[72px]' },
  { label: 'Phòng ban', key: 'department_name', class: 'min-w-[120px]' },
  { label: 'Ngày công', key: 'work_date', class: 'min-w-[104px] whitespace-nowrap' },
  { label: 'Ca làm', key: 'shift_info', class: 'min-w-[170px]' },
  { label: 'Giờ chuẩn', key: 'standard_minutes', align: 'text-center', class: 'min-w-[130px]' },
  { label: 'Check in', key: 'check_in_at', class: 'min-w-[118px] whitespace-nowrap' },
  { label: 'Check out', key: 'check_out_at', class: 'min-w-[96px] whitespace-nowrap' },
  { label: 'Giờ làm', key: 'worked_minutes', align: 'text-center', class: 'min-w-[88px] whitespace-nowrap' },
  { label: 'Đi muộn', key: 'late_minutes', align: 'text-center', class: 'min-w-[88px] whitespace-nowrap' },
  { label: 'Về sớm', key: 'early_leave_minutes', align: 'text-center', class: 'min-w-[88px] whitespace-nowrap' },
  { label: 'Trạng thái ngày', key: 'day_status', align: 'text-center', class: 'min-w-[150px]' },
  { label: 'Don', key: 'request_presence', align: 'text-center', class: 'min-w-[136px]' },
  { label: 'Duyệt', key: 'approval_status', align: 'text-center', class: 'min-w-[92px]' },
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
  { label: 'Ca lam', key: 'shift_info' },
  { label: 'Gio chuan', key: 'standard_minutes', align: 'text-center' },
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

const displayRequestColumns = computed(() => [
  ...(isLeaveApproval.value ? [{ label: '', key: 'selection', align: 'text-center', class: 'w-[60px] min-w-[60px]' }] : []),
  ...requestColumns,
])

const summaryCards = computed(() => {
  if (isLeaveApproval.value) {
    return [
      { label: 'Đơn nghỉ chờ duyệt', value: props.request_approvals.length },
      { label: 'Đã duyệt', value: props.reviewed_request_approvals.filter((item) => item.status === 'approved').length },
      { label: 'Từ chối', value: props.reviewed_request_approvals.filter((item) => item.status === 'rejected').length },
      { label: 'Lịch sử đơn', value: props.reviewed_request_approvals.length },
    ]
  }

  return [
    { label: 'Công chờ duyệt', value: props.approval_summary.pending_records ?? props.summary.pending_records ?? 0 },
    { label: 'Đã duyệt', value: props.approval_summary.approved_records ?? props.summary.confirmed_records ?? 0 },
    { label: 'Từ chối', value: props.approval_summary.rejected_records ?? props.summary.rejected_records ?? 0 },
    { label: 'Đơn chờ duyệt', value: props.request_approvals.length },
  ]
})

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
    buttonProps: {
      title: 'Duyệt ngày công',
      class: 'rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:border-emerald-300 hover:bg-emerald-100 hover:text-emerald-800',
    },
    hidden: (item) => item.approval_status !== 'pending'
      || item.display_approval_status === 'needs_verification'
      || (Number(item.employee_authority_level || 0) >= currentAuthorityLevel.value),
    onClick: (item) => decide(item, 'approve'),
  },
  {
    label: 'Từ chối',
    buttonProps: {
      title: 'Từ chối ngày công',
      class: 'rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:border-rose-300 hover:bg-rose-100 hover:text-rose-800',
    },
    hidden: (item) => item.approval_status !== 'pending' || (Number(item.employee_authority_level || 0) >= currentAuthorityLevel.value),
    onClick: (item) => decide(item, 'reject'),
  },
]

const requestActions = [
  {
    label: 'Chi tiết',
    buttonProps: {
      title: 'Xem chi tiết đơn',
      class: 'border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm hover:border-blue-300 hover:bg-blue-100 hover:text-blue-800',
    },
    onClick: (item) => openRequestDetail(item),
  },
  {
    label: 'Duyệt đơn',
    buttonProps: {
      title: 'Duyệt đơn',
      class: 'rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:border-emerald-300 hover:bg-emerald-100 hover:text-emerald-800',
    },
    hidden: (item) => item.status !== 'pending',
    onClick: (item) => reviewRequest(item, 'approve'),
  },
  {
    label: 'Từ chối',
    buttonProps: {
      title: 'Từ chối đơn',
      class: 'rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:border-rose-300 hover:bg-rose-100 hover:text-rose-800',
    },
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

watch(
  () => props.records.map((item) => item.id).join(','),
  () => {
    const availableIds = new Set(props.records.map((item) => item.id))
    selectedRecordIds.value = selectedRecordIds.value.filter((id) => availableIds.has(id))
  }
)

watch(
  () => props.request_approvals.map((item) => item.id).join(','),
  () => {
    const availableIds = new Set(props.request_approvals.map((item) => item.id))
    selectedApprovalRequestIds.value = selectedApprovalRequestIds.value.filter((id) => availableIds.has(id))
  }
)

function applyFilters() {
  router.get(route(isLeaveApproval.value ? 'leave.approvals' : 'attendance.approvals'), {
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
  decisionForm.resolved_check_out_time = ''

  if (action === 'approve' && needsResolvedCheckOut(item)) {
    const suggestedTime = suggestedCheckOutTime(item)
    const resolvedTime = window.prompt('Bản ghi thiếu check-out. Nhập giờ check-out để admin xác nhận công:', suggestedTime)
    if (resolvedTime === null) return
    if (!/^\d{2}:\d{2}$/.test(String(resolvedTime).trim())) {
      window.alert('Giờ check-out phải có định dạng HH:mm.')
      return
    }

    decisionForm.resolved_check_out_time = String(resolvedTime).trim()
  }

  decisionForm.post(route(action === 'approve' ? 'attendance.confirm' : 'attendance.reject', item.id), {
    preserveScroll: true,
  })
}

function needsResolvedCheckOut(item) {
  return Boolean(item.check_in_at) && (!item.check_out_at || item.missing_check_out || item.day_status === 'missing_check_out')
}

function isRecordSelectable(item) {
  return item.approval_status === 'pending'
    && Number(item.employee_authority_level || 0) < currentAuthorityLevel.value
}

function isRecordBulkApprovable(item) {
  return isRecordSelectable(item)
    && !needsResolvedCheckOut(item)
    && item.display_approval_status !== 'needs_verification'
}

function bulkSelectionTitle(item) {
  return bulkSelectionBlockReason(item) || 'Chon de duyet hang loat'
}

function bulkSelectionBlockReason(item) {
  if (!isRecordSelectable(item)) return 'Khong the chon do khong du quyen duyet'
  if (item.display_approval_status === 'needs_verification') return 'Nhan vien can giai trinh truoc khi duyet'
  if (needsResolvedCheckOut(item)) return 'Khong the chon vi thieu check-out'

  return ''
}

function bulkSelectionShortReason(item) {
  if (!isRecordSelectable(item)) return 'Khong du quyen'
  if (needsResolvedCheckOut(item)) return 'Thieu check-out'

  return ''
}

function toggleRecordSelection(item) {
  if (!isRecordBulkApprovable(item)) return

  if (selectedRecordIds.value.includes(item.id)) {
    selectedRecordIds.value = selectedRecordIds.value.filter((id) => id !== item.id)
    return
  }

  selectedRecordIds.value = [...selectedRecordIds.value, item.id]
}

function toggleCurrentPageSelection() {
  const pageIds = bulkApprovableRecordsOnPage.value.map((item) => item.id)
  if (!pageIds.length) return

  if (pageIds.every((id) => selectedRecordIds.value.includes(id))) {
    selectedRecordIds.value = selectedRecordIds.value.filter((id) => !pageIds.includes(id))
    return
  }

  selectedRecordIds.value = Array.from(new Set([...selectedRecordIds.value, ...pageIds]))
}

function approveSelectedRecords() {
  if (!selectedRecordIds.value.length) return

  const note = window.prompt('Ghi chu duyet hang loat:', 'Duyet hang loat')
  if (note === null) return
  if (String(note).trim().length < 5) {
    window.alert('Ghi chu toi thieu 5 ky tu.')
    return
  }

  bulkDecisionForm.record_ids = [...selectedRecordIds.value]
  bulkDecisionForm.note = String(note).trim()
  bulkDecisionForm.post(route('attendance.confirm-bulk'), {
    preserveScroll: true,
    onSuccess: () => {
      selectedRecordIds.value = []
    },
  })
}

function isRequestBulkApprovable(item) {
  return item.status === 'pending'
}

function toggleRequestSelection(item) {
  if (!isRequestBulkApprovable(item)) return

  if (selectedApprovalRequestIds.value.includes(item.id)) {
    selectedApprovalRequestIds.value = selectedApprovalRequestIds.value.filter((id) => id !== item.id)
    return
  }

  selectedApprovalRequestIds.value = [...selectedApprovalRequestIds.value, item.id]
}

function toggleCurrentRequestPageSelection() {
  const pageIds = bulkApprovableRequestsOnPage.value.map((item) => item.id)
  if (!pageIds.length) return

  if (pageIds.every((id) => selectedApprovalRequestIds.value.includes(id))) {
    selectedApprovalRequestIds.value = selectedApprovalRequestIds.value.filter((id) => !pageIds.includes(id))
    return
  }

  selectedApprovalRequestIds.value = Array.from(new Set([...selectedApprovalRequestIds.value, ...pageIds]))
}

function approveSelectedRequests() {
  if (!selectedApprovalRequestIds.value.length) return

  const note = window.prompt('Ghi chu duyet hang loat don nghi phep:', 'Duyet hang loat')
  if (note === null) return
  if (String(note).trim().length < 5) {
    window.alert('Ghi chu toi thieu 5 ky tu.')
    return
  }

  requestBulkDecisionForm.approval_request_ids = [...selectedApprovalRequestIds.value]
  requestBulkDecisionForm.note = String(note).trim()
  requestBulkDecisionForm.post(route('leave.request-approvals.approve-bulk'), {
    preserveScroll: true,
    onSuccess: () => {
      selectedApprovalRequestIds.value = []
    },
  })
}

function suggestedCheckOutTime(item) {
  return item.shift_end_time || '17:30'
}

function reviewRequest(item, action) {
  const note = window.prompt(action === 'approve' ? 'Ghi chú duyệt đơn:' : 'Lý do từ chối đơn:', item.reason || '')
  if (note === null) return
  if (String(note).trim().length < 5) {
    window.alert('Ghi chú tối thiểu 5 ký tự.')
    return
  }
  requestDecisionForm.note = String(note).trim()
  const routePrefix = isLeaveApproval.value ? 'leave' : 'attendance'
  requestDecisionForm.post(route(`${routePrefix}.request-approvals.${action === 'approve' ? 'approve' : 'reject'}`, item.id), {
    preserveScroll: true,
  })
}

function openRequestDetail(item) {
  selectedRequest.value = item
}

function hasRequestDetail(item) {
  return Boolean(item?.request_detail)
}

function openRequestDetailFromRecord(item) {
  if (!item?.request_detail) return

  openRequestDetail(item.request_detail)
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

function formatTimeRange(startTime, endTime) {
  if (!startTime && !endTime) return '-'

  return `${startTime || '--:--'} - ${endTime || '--:--'}`
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
  const value = typeof item === 'string' ? item : item?.day_status

  if (value === 'unpaid_leave' && item?.violation_status === 'missing_attendance') {
    return 'Vắng mặt'
  }

  const labels = {
    present: 'Đi làm',
    late: 'Đi muộn',
    early_leave: 'Về sớm',
    leave: 'Nghỉ phép',
    unpaid_leave: 'Nghỉ không lương',
    business_trip: 'Công tác',
    missing_check_in: 'Thiếu check in',
    missing_check_out: 'Thiếu check out',
    absent: 'Vắng mặt',
  }
  return labels[value] || '-'
}

function dayStatusBadges(item) {
  const badges = []
  const isOnTimeCheckIn = item.check_in_at
    && Number(item.late_minutes || 0) <= 0
    && ['on_time', 'present'].includes(String(item.attendance_status || ''))

  if (isOnTimeCheckIn) {
    badges.push({
      label: 'Đi làm đúng giờ',
      class: 'bg-emerald-50 text-emerald-700',
    })
  }

  if (item.day_status === 'missing_check_out') {
    badges.push({
      label: 'Thiếu check out',
      class: dayStatusClass('missing_check_out'),
    })
  } else if (!isOnTimeCheckIn || item.day_status !== 'present') {
    badges.push({
      label: formatDayStatus(item),
      class: dayStatusClass(item.day_status),
    })
  }

  return badges.length ? badges : [{
    label: '-',
    class: dayStatusClass(null),
  }]
}

function formatApprovalStatus(value) {
  if (value === 'needs_verification') {
    return 'Cần xác minh'
  }

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
    absent: 'bg-slate-100 text-slate-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}

function approvalStatusClass(value) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700',
    needs_verification: 'bg-orange-50 text-orange-700',
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


