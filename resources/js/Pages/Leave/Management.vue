<template>
  <Head title="Quan ly nghi phep" />

  <AdminLayout>
    <PageBreadcrumb title="Quan ly nghi phep" :items="[{ text: 'Cham cong', link: null }, { text: 'Nghi phep', link: null }]" />

    <section class="mb-4 rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-sm text-blue-900">
      <span class="font-semibold">Tong quan:</span> {{ summaryScopeLabel }}
    </section>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
      <SummaryCard label="Duoc huong" :value="formatDays(summary.total_entitled)" />
      <SummaryCard label="Da su dung" :value="formatDays(summary.total_used)" />
      <SummaryCard label="Dang cho duyet" :value="formatDays(summary.total_pending)" />
      <SummaryCard label="Con kha dung" :value="formatDays(summary.total_available)" />
    </div>

    <section class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-1">
        <h3 class="text-lg font-semibold text-gray-900">Danh muc loai nghi</h3>
      </div>

      <form class="mt-5 grid grid-cols-1 gap-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm" @submit.prevent="submitCreateType">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-12">
          <div class="md:col-span-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <Field label="Ten loai nghi" :error="createTypeForm.errors.name">
              <input v-model.trim="createTypeForm.name" class="form-input" placeholder="Nghi phep nam">
            </Field>
            <Field label="So ngay phep/nam" :error="createTypeForm.errors.annual_quota">
              <input v-model.number="createTypeForm.annual_quota" class="form-input" type="number" min="0" max="365" step="0.5">
            </Field>
            <Field label="Toi da/don" :error="createTypeForm.errors.max_days_per_request">
              <div class="relative">
                <input
                  v-if="createTypeForm.has_request_limit"
                  v-model.number="createTypeForm.max_days_per_request"
                  class="form-input"
                  type="number"
                  min="0.5"
                  max="365"
                  step="0.5"
                  placeholder="So ngay"
                >
                <input
                  v-else
                  class="form-input form-input-readonly"
                  type="text"
                  value="Khong gioi han"
                  readonly
                >
              </div>
            </Field>
            <Field label="Mo ta" :error="createTypeForm.errors.description">
              <input v-model.trim="createTypeForm.description" class="form-input" placeholder="Mo ta ngan">
            </Field>
          </div>

          <div class="md:col-span-4">
            <OptionGroup title="Trang thai va quy dinh" description="Cau hinh cac quy tac cho loai nghi nay.">
              <div class="grid grid-cols-1 gap-2">
                <OptionCheck v-model="createTypeForm.is_active" label="Dang dung" description="Cho phep su dung." />
                <OptionCheck v-model="createTypeForm.is_paid" label="Co luong" description="Co tinh luong." />
                <OptionCheck
                  v-model="createTypeForm.deducts_balance"
                  label="Tru quy"
                  description="Tru phep ton."
                  :disabled="!createTypeForm.is_paid"
                />
                <OptionCheck v-model="createTypeForm.requires_attachment" label="Can minh chung" description="Bat buoc tep." />
                <OptionCheck v-model="createTypeForm.has_request_limit" label="Gioi han/don" description="So ngay toi da." />
                <OptionCheck
                  v-model="createTypeForm.prorate_by_hire_date"
                  label="Prorate"
                  description="Theo ngay vao."
                  :disabled="!canProrate(createTypeForm)"
                />
              </div>
            </OptionGroup>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60 shadow-md shadow-blue-200" :disabled="createTypeForm.processing">
            <template v-if="createTypeForm.processing">
              <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Dang them...
            </template>
            <template v-else>
              Them loai nghi
            </template>
          </button>
        </div>
      </form>

      <TableShell class="mt-5">
        <thead>
          <tr class="text-left">
            <th class="p-2">Loai nghi</th>
            <th class="p-2">Quy tac</th>
            <th class="p-2">So ngay phep/nam</th>
            <th class="p-2">Toi da moi don</th>
            <th class="p-2">Trang thai</th>
            <th class="p-2">Tac vu</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in leave_types" :key="item.id" class="border-t">
            <td class="p-2">
              <div class="font-semibold text-gray-900">{{ item.name }}</div>
              <div class="text-xs text-gray-500">{{ item.code }}</div>
            </td>
            <td class="p-2 text-sm">
              <div>{{ item.is_paid ? 'Co luong' : 'Khong luong' }}</div>
              <div class="text-gray-500">{{ item.deducts_balance ? 'Tru quy phep' : 'Khong tru quy' }}{{ item.requires_attachment ? ' | Can minh chung' : '' }}</div>
            </td>
            <td class="p-2">{{ formatDays(item.annual_quota) }}</td>
            <td class="p-2">{{ item.max_days_per_request === null ? 'Khong gioi han' : formatDays(item.max_days_per_request) }}</td>
            <td class="p-2">
              <span :class="item.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600'" class="rounded-full px-3 py-1 text-xs font-semibold">
                {{ item.is_active ? 'Dang dung' : 'Ngung dung' }}
              </span>
            </td>
            <td class="p-2 space-x-2">
              <button type="button" class="rounded border px-3 py-1 text-sm" @click="openEditType(item)">Sua</button>
              <button type="button" class="rounded border px-3 py-1 text-sm" @click="toggleType(item.id)">{{ item.is_active ? 'Ngung' : 'Kich hoat' }}</button>
            </td>
          </tr>
        </tbody>
      </TableShell>
    </section>

    <section class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-1">
        <h3 class="text-lg font-semibold text-gray-900">So du phep nhan vien</h3>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-4">
        <Field label="Nam">
          <input v-model.number="filterForm.year" class="form-input" type="number" min="2000" max="2100" @change="applyFilters">
        </Field>
        <Field label="Nhan vien">
          <select v-model="filterForm.employee_profile_id" class="form-input" @change="applyFilters">
            <option :value="null">Tat ca</option>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.label }}</option>
          </select>
        </Field>
        <Field label="Loai nghi">
          <select v-model="filterForm.leave_type_id" class="form-input" @change="applyFilters">
            <option :value="null">Tat ca</option>
            <option v-for="type in leave_types" :key="type.id" :value="type.id">{{ type.name }}</option>
          </select>
        </Field>
      </div>

      <form class="mt-5 grid grid-cols-1 gap-4 rounded-xl border border-blue-100 bg-blue-50/70 p-4 md:grid-cols-4" @submit.prevent="submitGrant">
        <div class="text-sm font-semibold text-blue-900 md:col-span-4">Tao/cap nhat so du tu dong</div>
        <Field label="Nhan vien" :error="grantForm.errors.employee_profile_id">
          <select v-model="grantForm.employee_profile_id" class="form-input">
            <option value="">Chon nhan vien</option>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.label }}</option>
          </select>
        </Field>
        <Field label="Loai nghi" :error="grantForm.errors.leave_type_id">
          <select v-model="grantForm.leave_type_id" class="form-input">
            <option value="">Chon loai nghi</option>
            <option v-for="type in active_leave_types" :key="type.id" :value="type.id">{{ type.name }} - {{ formatDays(type.annual_quota) }}/nam</option>
          </select>
        </Field>
        <Field label="Nam" :error="grantForm.errors.year">
          <input v-model.number="grantForm.year" class="form-input" type="number" min="2000" max="2100">
        </Field>
        <div class="flex items-end justify-end">
          <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="grantForm.processing">
            Tao so du tu dong
          </button>
        </div>
      </form>

      <form class="mt-4 grid grid-cols-1 gap-4 rounded-xl border border-emerald-100 bg-emerald-50/70 p-4 md:grid-cols-4" @submit.prevent="submitBulkGrant">
        <div class="text-sm font-semibold text-emerald-900 md:col-span-4">Cap/dong bo hang loat</div>
        <Field label="Nam" :error="bulkGrantForm.errors.year">
          <input v-model.number="bulkGrantForm.year" class="form-input" type="number" min="2000" max="2100">
        </Field>
        <Field label="Nhan vien">
          <select v-model="bulkGrantForm.employee_profile_id" class="form-input">
            <option :value="null">Tat ca nhan vien</option>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.label }}</option>
          </select>
        </Field>
        <Field label="Loai nghi">
          <select v-model="bulkGrantForm.leave_type_id" class="form-input">
            <option :value="null">Tat ca loai nghi</option>
            <option v-for="type in active_leave_types" :key="type.id" :value="type.id">{{ type.name }}</option>
          </select>
        </Field>
        <div class="flex items-end justify-end">
          <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="bulkGrantForm.processing">
            Dong bo hang loat
          </button>
        </div>
      </form>

      <TableShell class="mt-5">
        <thead>
          <tr class="text-left">
            <th class="p-2">Nhan vien</th>
            <th class="p-2">Loai nghi</th>
            <th class="p-2">Nam</th>
            <th class="p-2">Duoc huong</th>
            <th class="p-2">Dieu chinh</th>
            <th class="p-2">Da dung</th>
            <th class="p-2">Cho duyet</th>
            <th class="p-2">Con lai</th>
            <th class="p-2">Tac vu</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in paginatedBalances" :key="item.id" class="border-t">
            <td class="p-2">
              <div class="font-semibold text-gray-900">{{ item.employee_name }}</div>
              <div class="text-xs text-gray-500">{{ item.employee_code }} | {{ item.department_name || '-' }}</div>
            </td>
            <td class="p-2">{{ item.leave_type_name }}</td>
            <td class="p-2">{{ item.year }}</td>
            <td class="p-2">{{ formatDays(Number(item.opening_balance) + Number(item.accrued_days)) }}</td>
            <td class="p-2">{{ formatDays(item.adjusted_days) }}</td>
            <td class="p-2">{{ formatDays(item.used_days) }}</td>
            <td class="p-2">{{ formatDays(item.pending_days) }}</td>
            <td class="p-2 font-semibold text-emerald-700">{{ formatDays(item.available_days) }}</td>
            <td class="p-2">
              <button type="button" class="rounded border px-3 py-1 text-sm" @click="openAdjust(item)">Dieu chinh</button>
            </td>
          </tr>
        </tbody>
      </TableShell>
      <LocalPagination
        v-if="balances.length"
        class="mt-4"
        label="so du"
        :total="balances.length"
        :page="pagination.balances"
        :per-page="perPage.balances"
        :options="perPageOptions"
        @page="setPage('balances', $event)"
        @per-page="setPerPage('balances', $event)"
      />
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <h3 class="text-lg font-semibold text-gray-900">Lich su bien dong quy phep</h3>
      <TableShell class="mt-5">
        <thead>
          <tr class="text-left">
            <th class="p-2">Thoi gian</th>
            <th class="p-2">Nhan vien</th>
            <th class="p-2">Loai nghi</th>
            <th class="p-2">Loai bien dong</th>
            <th class="p-2">So ngay</th>
            <th class="p-2">Con lai sau GD</th>
            <th class="p-2">Ghi chu</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in paginatedTransactions" :key="item.id" class="border-t">
            <td class="p-2">{{ formatDateTime(item.created_at) }}</td>
            <td class="p-2">{{ item.employee_name || '-' }}</td>
            <td class="p-2">{{ item.leave_type_name || '-' }}</td>
            <td class="p-2">{{ transactionLabel(item.type) }}</td>
            <td class="p-2">{{ formatDays(item.days) }}</td>
            <td class="p-2">{{ item.balance_after === null ? '-' : formatDays(item.balance_after) }}</td>
            <td class="p-2">{{ item.note || '-' }}</td>
          </tr>
        </tbody>
      </TableShell>
      <LocalPagination
        v-if="recent_transactions.length"
        class="mt-4"
        label="giao dich"
        :total="recent_transactions.length"
        :page="pagination.transactions"
        :per-page="perPage.transactions"
        :options="perPageOptions"
        @page="setPage('transactions', $event)"
        @per-page="setPerPage('transactions', $event)"
      />
    </section>

    <div v-if="editingType" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-md bg-gray-900/40 transition-all duration-300">
      <form class="w-full max-w-5xl transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all" @submit.prevent="submitEditType">
        <div class="flex items-center justify-between bg-gray-50/50 px-8 py-6 border-b border-gray-100">
          <div>
            <h3 class="text-xl font-bold text-gray-900">Chinh sua loai nghi</h3>
            <p class="mt-1 text-sm text-gray-500 font-medium">{{ editingType.name }}</p>
          </div>
          <button type="button" class="group flex h-10 w-10 items-center justify-center rounded-full transition hover:bg-gray-200" @click="closeEditType">
            <svg class="h-6 w-6 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <div class="grid grid-cols-1 gap-0 xl:grid-cols-12">
          <div class="xl:col-span-8 p-8 space-y-8">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              <Field label="Ten loai nghi" :error="editTypeForm.errors.name">
                <input v-model.trim="editTypeForm.name" class="form-input">
              </Field>
              <Field label="So ngay phep/nam" :error="editTypeForm.errors.annual_quota">
                <input v-model.number="editTypeForm.annual_quota" class="form-input" type="number" min="0" max="365" step="0.5">
              </Field>
              <Field label="Toi da/don" :error="editTypeForm.errors.max_days_per_request">
                <input
                  v-if="editTypeForm.has_request_limit"
                  v-model.number="editTypeForm.max_days_per_request"
                  class="form-input"
                  type="number"
                  min="0.5"
                  max="365"
                  step="0.5"
                  placeholder="So ngay"
                >
                <input
                  v-else
                  class="form-input form-input-readonly"
                  type="text"
                  value="Khong gioi han"
                  readonly
                >
              </Field>
              <Field label="Mo ta" :error="editTypeForm.errors.description">
                <input v-model.trim="editTypeForm.description" class="form-input" placeholder="Ghi chu ngan">
              </Field>
            </div>

            <div class="rounded-2xl bg-blue-50/50 p-4 border border-blue-100/50">
              <div class="flex items-center gap-3 text-blue-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm font-semibold">Huong dan quy dac</p>
              </div>
              <p class="mt-1 text-xs text-blue-600/80 leading-relaxed">
                Dam bao ma loai la duy nhat va ten goi ro rang. Cac thiet lap ben phai se anh huong truc tiep den quyen loi va luong cua nhan vien khi su dung loai nghi nay.
              </p>
            </div>
          </div>

          <div class="xl:col-span-4 bg-gray-50/30 p-8 border-l border-gray-100">
            <OptionGroup title="Trang thai va quy dinh" description="Cau hinh nang cao de tranh dat nham rule.">
              <div class="grid grid-cols-1 gap-3">
                <OptionCheck v-model="editTypeForm.is_active" label="Dang dung" description="Cho phep nhan vien tiep tuc dung." />
                <OptionCheck v-model="editTypeForm.is_paid" label="Co luong" description="Ngay nghi duoc tinh luong." />
                <OptionCheck
                  v-model="editTypeForm.deducts_balance"
                  label="Tru quy"
                  description="Tru vao quy phep con lai."
                  :disabled="!editTypeForm.is_paid"
                />
                <OptionCheck v-model="editTypeForm.requires_attachment" label="Can minh chung" description="Bat buoc dinh kem tai lieu." />
                <OptionCheck v-model="editTypeForm.has_request_limit" label="Gioi han/don" description="Nhap so ngay toi da." />
                <OptionCheck
                  v-model="editTypeForm.prorate_by_hire_date"
                  label="Prorate"
                  description="Cap phat theo ngay vao lam."
                  :disabled="!canProrate(editTypeForm)"
                />
              </div>
            </OptionGroup>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 bg-gray-50/50 px-8 py-6 border-t border-gray-100">
          <button type="button" class="rounded-xl border border-gray-300 px-6 py-2.5 text-sm font-bold text-gray-700 transition hover:bg-gray-50" @click="closeEditType">Huy</button>
          <button class="rounded-xl bg-blue-600 px-8 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:opacity-60 shadow-lg shadow-blue-200" :disabled="editTypeForm.processing">
            {{ editTypeForm.processing ? 'Dang luu...' : 'Luu thay doi' }}
          </button>
        </div>
      </form>
    </div>

    <div v-if="adjustingBalance" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <form class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl" @submit.prevent="submitAdjust">
        <h3 class="text-lg font-semibold text-gray-900">Dieu chinh quy phep</h3>
        <p class="mt-1 text-sm text-gray-500">{{ adjustingBalance.employee_name }} - {{ adjustingBalance.leave_type_name }}</p>
        <div class="mt-4 space-y-4">
          <Field label="So ngay dieu chinh (+ tang, - giam)" :error="adjustForm.errors.days">
            <input v-model.number="adjustForm.days" class="form-input" type="number" step="0.5">
          </Field>
          <Field label="Ly do dieu chinh" :error="adjustForm.errors.note">
            <textarea v-model.trim="adjustForm.note" class="form-input min-h-[88px]"></textarea>
          </Field>
        </div>
        <div class="mt-5 flex justify-end gap-3">
          <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700" @click="closeAdjust">Huy</button>
          <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="adjustForm.processing">Luu dieu chinh</button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, h, reactive, ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  leave_types: { type: Array, default: () => [] },
  active_leave_types: { type: Array, default: () => [] },
  employees: { type: Array, default: () => [] },
  balances: { type: Array, default: () => [] },
  recent_transactions: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
})

const editingType = ref(null)
const adjustingBalance = ref(null)
const perPageOptions = [10, 20, 50]
const pagination = reactive({
  balances: 1,
  transactions: 1,
})
const perPage = reactive({
  balances: 10,
  transactions: 10,
})

const filterForm = reactive({
  year: Number(props.filters.year),
  employee_profile_id: props.filters.employee_profile_id ?? null,
  leave_type_id: props.filters.leave_type_id ?? null,
})

const createTypeForm = useForm(defaultTypeForm())
const editTypeForm = useForm(defaultTypeForm())

const grantForm = useForm({
  employee_profile_id: '',
  leave_type_id: '',
  year: Number(props.filters.year),
  note: '',
})

const bulkGrantForm = useForm({
  employee_profile_id: null,
  leave_type_id: null,
  year: Number(props.filters.year),
})

const adjustForm = useForm({
  days: 0,
  note: '',
})

const summaryScopeLabel = computed(() => {
  const employee = props.employees.find((item) => Number(item.id) === Number(filterForm.employee_profile_id))
  const leaveType = props.leave_types.find((item) => Number(item.id) === Number(filterForm.leave_type_id))
  const parts = [`nam ${filterForm.year}`]

  parts.push(employee ? `nhan vien ${employee.label}` : 'tat ca nhan vien')
  parts.push(leaveType ? `loai nghi ${leaveType.name}` : 'tat ca loai nghi')

  return `Dang xem theo ${parts.join(', ')}`
})

const paginatedBalances = computed(() => paginateItems(props.balances, 'balances'))
const paginatedTransactions = computed(() => paginateItems(props.recent_transactions, 'transactions'))

watch(() => [createTypeForm.is_paid, createTypeForm.deducts_balance, createTypeForm.annual_quota, createTypeForm.has_request_limit], () => {
  normalizeTypeForm(createTypeForm)
})

watch(() => [editTypeForm.is_paid, editTypeForm.deducts_balance, editTypeForm.annual_quota, editTypeForm.has_request_limit], () => {
  normalizeTypeForm(editTypeForm)
})

watch(() => createTypeForm.name, (value) => {
  createTypeForm.code = generateLeaveTypeCode(value)
})

watch(() => editTypeForm.name, (value) => {
  editTypeForm.code = generateLeaveTypeCode(value)
})

function defaultTypeForm() {
  return {
    code: '',
    name: '',
    is_paid: true,
    deducts_balance: true,
    requires_attachment: false,
    annual_quota: 0,
    prorate_by_hire_date: true,
    has_request_limit: false,
    max_days_per_request: null,
    carryover_limit: null,
    description: '',
    is_active: true,
  }
}

function typePayload(form) {
  normalizeTypeForm(form)
  form.code = generateLeaveTypeCode(form.name)

  return {
    code: form.code,
    name: form.name,
    is_paid: form.is_paid,
    deducts_balance: form.deducts_balance,
    requires_attachment: form.requires_attachment,
    annual_quota: Number(form.annual_quota || 0),
    prorate_by_hire_date: form.prorate_by_hire_date,
    max_days_per_request: form.has_request_limit ? Number(form.max_days_per_request || 0) : null,
    carryover_limit: null,
    description: form.description,
    is_active: form.is_active,
  }
}

function generateLeaveTypeCode(value) {
  const normalized = String(value || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-zA-Z0-9]+/g, '_')
    .replace(/^_+|_+$/g, '')
    .replace(/_+/g, '_')
    .toUpperCase()

  return normalized
}

function canProrate(form) {
  return Boolean(form.is_paid && form.deducts_balance && Number(form.annual_quota || 0) > 0)
}

function normalizeTypeForm(form) {
  if (!form.is_paid) {
    form.deducts_balance = false
  }

  if (!canProrate(form)) {
    form.prorate_by_hire_date = false
  }

  form.carryover_limit = null

  if (!form.has_request_limit) {
    form.max_days_per_request = null
  }
}

function applyFilters() {
  router.get(route('leave-management.index'), {
    year: filterForm.year,
    employee_profile_id: filterForm.employee_profile_id,
    leave_type_id: filterForm.leave_type_id,
  }, {
    preserveScroll: true,
    preserveState: true,
  })
}

function paginateItems(items, key) {
  const page = pagination[key] || 1
  const limit = perPage[key] || 10
  const start = (page - 1) * limit

  return (items || []).slice(start, start + limit)
}

function totalPagesFor(key) {
  const totals = {
    balances: props.balances.length,
    transactions: props.recent_transactions.length,
  }

  return Math.max(1, Math.ceil((totals[key] || 0) / (perPage[key] || 10)))
}

function setPage(key, page) {
  pagination[key] = Math.min(Math.max(1, Number(page) || 1), totalPagesFor(key))
}

function setPerPage(key, value) {
  perPage[key] = Number(value) || 10
  pagination[key] = 1
}

function submitCreateType() {
  createTypeForm
    .transform(() => typePayload(createTypeForm))
    .post(route('leave-management.types.store'), {
      preserveScroll: true,
      onSuccess: () => {
        createTypeForm.defaults(defaultTypeForm())
        createTypeForm.reset()
      },
    })
}

function openEditType(item) {
  editingType.value = item
  editTypeForm.defaults({
    code: generateLeaveTypeCode(item.name),
    name: item.name,
    is_paid: item.is_paid,
    deducts_balance: item.deducts_balance,
    requires_attachment: item.requires_attachment,
    annual_quota: item.annual_quota,
    prorate_by_hire_date: item.prorate_by_hire_date,
    has_request_limit: item.max_days_per_request !== null,
    max_days_per_request: item.max_days_per_request,
    carryover_limit: null,
    description: item.description || '',
    is_active: item.is_active,
  })
  editTypeForm.reset()
  editTypeForm.code = generateLeaveTypeCode(editTypeForm.name)
  normalizeTypeForm(editTypeForm)
  editTypeForm.clearErrors()
}

function closeEditType() {
  editingType.value = null
  editTypeForm.defaults(defaultTypeForm())
  editTypeForm.reset()
  editTypeForm.clearErrors()
}

function submitEditType() {
  if (!editingType.value) return

  editTypeForm
    .transform(() => typePayload(editTypeForm))
    .put(route('leave-management.types.update', editingType.value.id), {
      preserveScroll: true,
      onSuccess: closeEditType,
    })
}

function toggleType(id) {
  router.put(route('leave-management.types.toggle', id), {}, { preserveScroll: true })
}

function submitGrant() {
  grantForm.post(route('leave-management.balances.grant'), {
    preserveScroll: true,
    onSuccess: () => grantForm.reset('employee_profile_id', 'leave_type_id', 'note'),
  })
}

function submitBulkGrant() {
  bulkGrantForm.post(route('leave-management.balances.grant-bulk'), {
    preserveScroll: true,
  })
}

function openAdjust(item) {
  adjustingBalance.value = item
  adjustForm.reset()
  adjustForm.clearErrors()
}

function closeAdjust() {
  adjustingBalance.value = null
  adjustForm.reset()
  adjustForm.clearErrors()
}

function submitAdjust() {
  if (!adjustingBalance.value) return

  adjustForm.post(route('leave-management.balances.adjust', adjustingBalance.value.id), {
    preserveScroll: true,
    onSuccess: closeAdjust,
  })
}

function formatDays(value) {
  const number = Number(value || 0)
  return `${Number.isInteger(number) ? number : number.toFixed(1)} ngay`
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

function transactionLabel(type) {
  return {
    grant: 'Tao/cap nhat tu dong',
    adjust: 'Dieu chinh',
    pending: 'Giu cho duyet',
    approve: 'Da duyet',
    reject: 'Hoan do tu choi',
  }[type] || type
}

const SummaryCard = {
  props: { label: String, value: String },
  setup(componentProps) {
    return () => h('div', { class: 'rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm' }, [
      h('div', { class: 'text-sm font-medium text-gray-500' }, componentProps.label),
      h('div', { class: 'mt-2 text-2xl font-semibold text-gray-900' }, componentProps.value),
    ])
  },
}

const Field = {
  props: { label: String, error: String },
  setup(componentProps, { slots }) {
    return () => h('label', { class: 'block' }, [
      h('span', { class: 'mb-2 block text-sm font-medium text-gray-700' }, componentProps.label),
      slots.default?.(),
      componentProps.error ? h('span', { class: 'mt-1 block text-sm text-red-500' }, componentProps.error) : null,
    ])
  },
}

const OptionGroup = {
  props: { title: String, description: String },
  setup(componentProps, { slots }) {
    return () => h('section', { class: 'space-y-4' }, [
      h('div', { class: 'px-1' }, [
        h('div', { class: 'text-base font-bold text-gray-900' }, componentProps.title),
        componentProps.description ? h('p', { class: 'mt-1 text-xs font-medium text-gray-500 italic' }, componentProps.description) : null,
      ]),
      h('div', { class: 'space-y-3' }, slots.default?.()),
    ])
  },
}

const OptionCheck = {
  props: { modelValue: Boolean, label: String, description: String, disabled: Boolean },
  emits: ['update:modelValue'],
  setup(componentProps, { emit }) {
    return () => h('label', {
      class: `flex items-center gap-4 rounded-2xl border-2 p-4 transition-all duration-200 ${
        componentProps.modelValue
          ? 'border-blue-500 bg-blue-50/50 shadow-sm shadow-blue-100'
          : 'border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50/50'
      } ${componentProps.disabled ? 'cursor-not-allowed opacity-40 greyscale' : 'cursor-pointer active:scale-[0.98]'}`,
    }, [
      h('div', { class: 'relative flex h-6 w-6 shrink-0 items-center justify-center' }, [
        h('input', {
          type: 'checkbox',
          class: 'h-5 w-5 rounded-md border-2 border-gray-300 text-blue-600 transition-all focus:ring-4 focus:ring-blue-100 checked:border-blue-600',
          checked: componentProps.modelValue,
          disabled: componentProps.disabled,
          onChange: (event) => emit('update:modelValue', event.target.checked),
        }),
      ]),
      h('div', { class: 'min-w-0 flex-1' }, [
        h('div', { class: `text-sm font-bold ${componentProps.modelValue ? 'text-blue-900' : 'text-gray-900'}` }, componentProps.label),
        componentProps.description ? h('div', { class: `mt-0.5 text-[11px] font-medium leading-tight ${componentProps.modelValue ? 'text-blue-600/80' : 'text-gray-500'}` }, componentProps.description) : null,
      ]),
    ])
  },
}

const TableShell = {
  setup(_, { slots }) {
    return () => h('div', { class: 'overflow-x-auto rounded-xl border border-gray-100' }, [
      h('table', { class: 'min-w-full text-sm' }, slots.default?.()),
    ])
  },
}

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

    return () => h('div', { class: 'flex flex-col gap-3 border-t border-gray-100 pt-4 md:flex-row md:items-center md:justify-between' }, [
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
</script>

<style scoped>
.form-input {
  width: 100%;
  border-radius: 0.75rem;
  border: 2px solid #f3f4f6;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  min-height: 48px;
  background: #fff;
  transition: all 0.2s ease;
  color: #111827;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
  font-weight: 400;
}

.form-input[type='number'] {
  appearance: textfield;
}

.form-input[type='number']::-webkit-outer-spin-button,
.form-input[type='number']::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.form-input-readonly {
  color: #6b7280;
  background: #f9fafb;
  border-color: #f3f4f6;
  cursor: not-allowed;
}
</style>
