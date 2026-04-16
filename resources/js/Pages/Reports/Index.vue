<template>
  <AdminLayout title="Bao cao tong hop">
    <PageBreadcrumb title="Bao cao tong hop" :items="[{ text: 'Bao cao', link: null }]" />

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4">
      <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Thang</label>
          <select v-model="form.month" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option v-for="m in 12" :key="m" :value="m">Thang {{ m }}</option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Nam</label>
          <input v-model.number="form.year" type="number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
        </div>
        <div v-if="canViewAll">
          <label class="mb-1 block text-sm font-medium text-gray-700">Phong ban</label>
          <select v-model="form.department_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Tat ca</option>
            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>
        <div v-if="canViewProjectReports">
          <label class="mb-1 block text-sm font-medium text-gray-700">Trang thai du an</label>
          <select v-model="form.project_status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Tat ca</option>
            <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button @click="applyFilters" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Xem bao cao</button>
          <a :href="exportExcelUrl" class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Xuat Excel</a>
          <a :href="exportPdfUrl" class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">Xuat PDF</a>
        </div>
      </div>
      <div class="mt-2 text-xs text-gray-500">{{ scopeLabel }}</div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
      <div class="rounded-xl border border-gray-200 bg-white p-4">
        <h3 class="mb-3 text-base font-semibold text-gray-900">Bao cao nhan su theo phong ban</h3>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Phong ban</th>
              <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">So nhan su</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in employeeByDepartment" :key="item.department_name">
              <td class="px-3 py-2">{{ item.department_name }}</td>
              <td class="px-3 py-2 font-semibold">{{ item.employee_count }}</td>
            </tr>
            <tr v-if="!employeeByDepartment.length"><td colspan="2" class="px-3 py-4 text-center text-gray-500">Khong co du lieu</td></tr>
          </tbody>
        </table>
      </div>

      <div v-if="canViewProjectReports" class="rounded-xl border border-gray-200 bg-white p-4">
        <h3 class="mb-3 text-base font-semibold text-gray-900">Bao cao du an theo trang thai</h3>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Trang thai</th>
              <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">So luong</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in projectByStatus" :key="item.status">
              <td class="px-3 py-2">{{ item.status_label }}</td>
              <td class="px-3 py-2 font-semibold">{{ item.count }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="canViewProjectReports" class="mt-4 rounded-xl border border-gray-200 bg-white p-4">
      <h3 class="mb-3 text-base font-semibold text-gray-900">Bao cao tien do du an</h3>
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Du an</th>
            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Trang thai</th>
            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Tien do</th>
            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Dau viec</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="item in projectProgress" :key="`${item.project_name}-${item.start_date}`">
            <td class="px-3 py-2">{{ item.project_name }}</td>
            <td class="px-3 py-2">{{ item.status_label }}</td>
            <td class="px-3 py-2 font-semibold">{{ item.progress_percent }}%</td>
            <td class="px-3 py-2">{{ item.completed_tasks }}/{{ item.total_tasks }}</td>
          </tr>
          <tr v-if="!projectProgress.length"><td colspan="4" class="px-3 py-4 text-center text-gray-500">Khong co du lieu</td></tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 rounded-xl border border-gray-200 bg-white p-4">
      <h3 class="mb-3 text-base font-semibold text-gray-900">Bao cao cham cong thang</h3>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-6">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3"><div class="text-xs text-gray-500">Tong ban ghi</div><div class="mt-1 text-lg font-semibold">{{ attendanceMonthly.total_records || 0 }}</div></div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3"><div class="text-xs text-gray-500">Dung gio</div><div class="mt-1 text-lg font-semibold text-emerald-700">{{ attendanceMonthly.on_time_records || 0 }}</div></div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3"><div class="text-xs text-gray-500">Di muon</div><div class="mt-1 text-lg font-semibold text-amber-700">{{ attendanceMonthly.late_records || 0 }}</div></div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3"><div class="text-xs text-gray-500">Ve som</div><div class="mt-1 text-lg font-semibold text-orange-700">{{ attendanceMonthly.early_leave_records || 0 }}</div></div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3"><div class="text-xs text-gray-500">Vang mat</div><div class="mt-1 text-lg font-semibold text-rose-700">{{ attendanceMonthly.absent_records || 0 }}</div></div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3"><div class="text-xs text-gray-500">Tong gio lam</div><div class="mt-1 text-sm font-semibold">{{ workedHoursLabel }}</div></div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  departments: { type: Array, default: () => [] },
  statusOptions: { type: Array, default: () => [] },
  employeeByDepartment: { type: Array, default: () => [] },
  projectByStatus: { type: Array, default: () => [] },
  projectProgress: { type: Array, default: () => [] },
  attendanceMonthly: { type: Object, default: () => ({}) },
  scopeLabel: { type: String, default: '' },
  canViewAll: { type: Boolean, default: false },
  canViewProjectReports: { type: Boolean, default: true },
})

const form = reactive({
  month: props.filters.month ?? new Date().getMonth() + 1,
  year: props.filters.year ?? new Date().getFullYear(),
  department_id: props.filters.department_id ?? '',
  project_status: props.filters.project_status ?? '',
})

const query = computed(() => ({
  month: form.month,
  year: form.year,
  department_id: form.department_id || undefined,
  project_status: form.project_status || undefined,
}))

const exportExcelUrl = computed(() => route('reports.export.excel', query.value))
const exportPdfUrl = computed(() => route('reports.export.pdf', query.value))

const workedHoursLabel = computed(() => {
  const minutes = Number(props.attendanceMonthly?.worked_minutes || 0)
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return `${h} gio ${m} phut`
})

const applyFilters = () => {
  router.get(route('reports.index'), query.value, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>
