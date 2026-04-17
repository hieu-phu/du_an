<template>
  <Head title="Dashboard" />

  <AdminLayout>
    <PageBreadcrumb title="Dashboard" :items="[{ text: 'Dashboard', link: null }]" />

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Tong quan he thong</h2>
          <p class="mt-1 text-sm text-gray-500">Chuc vu hien tai: {{ positionLabel }}</p>
        </div>

        <div v-if="permissions['attendance.mine.action']" class="flex flex-wrap gap-3">
          <button
            v-if="canCheckIn"
            @click="checkIn"
            :disabled="attendanceForm.processing"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
          >
            {{ attendanceForm.processing ? 'Dang xu ly...' : 'Check in' }}
          </button>
          <button
            v-if="canCheckOut"
            @click="checkOut"
            :disabled="attendanceForm.processing"
            class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:opacity-60"
          >
            {{ attendanceForm.processing ? 'Dang xu ly...' : 'Check out' }}
          </button>
        </div>
      </div>

      <div v-if="todayAttendance" class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Ngay:</span>
            <span class="font-medium">{{ formatDate(todayAttendance.work_date) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Vao:</span>
            <span class="font-medium text-blue-600">{{ formatDateTime(todayAttendance.check_in_at) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Ra:</span>
            <span class="font-medium text-amber-600">{{ formatDateTime(todayAttendance.check_out_at) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Trang thai:</span>
            <span :class="['rounded-full px-2 py-0.5 text-xs font-semibold', getStatusClass(todayAttendance.status)]">
              {{ todayAttendance.status_label || 'Chua cham cong' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="warnings && warnings.length > 0" class="mb-6 overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm">
      <div class="flex items-center gap-2 border-b border-red-100 bg-red-50/50 px-4 py-3">
        <h3 class="text-sm font-bold uppercase tracking-wider text-red-900">Thong bao quan trong</h3>
        <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-200 text-[10px] font-bold text-red-800">
          {{ warnings.length }}
        </span>
      </div>
      <div class="divide-y divide-gray-100">
        <div
          v-for="(warning, index) in warnings"
          :key="index"
          class="group flex items-center gap-4 p-4 transition-colors hover:bg-gray-50/50"
        >
          <div class="min-w-0 flex-1">
            <h4 class="text-sm font-bold text-gray-900">{{ warning.title }}</h4>
            <p class="text-xs text-gray-500">{{ warning.message }}</p>
          </div>
          <div class="flex-shrink-0">
            <Link
              :href="warning.cta_url"
              class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-200"
            >
              {{ warning.cta_label }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div
        v-for="card in stats"
        :key="card.title"
        class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm"
      >
        <div class="text-sm text-gray-500">{{ card.title }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ card.value }}</div>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Tong hop du an</h3>
        <div class="mt-4 grid grid-cols-2 gap-3">
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Tong nhan su</div>
            <div class="mt-1 text-xl font-semibold text-gray-900">{{ dashboardSummary.total_employees || 0 }}</div>
          </div>
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Tong du an</div>
            <div class="mt-1 text-xl font-semibold text-gray-900">{{ dashboardSummary.total_projects || 0 }}</div>
          </div>
        </div>
        <div class="mt-4">
          <div class="mb-2 text-sm font-semibold text-gray-800">So luong du an theo trang thai</div>
          <div class="flex flex-wrap gap-2">
            <div
              v-for="item in projectStatusCounts"
              :key="item.status"
              class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700"
            >
              <span>{{ item.label }}</span>
              <span class="font-semibold text-gray-900">{{ item.count }}</span>
            </div>
            <div v-if="!projectStatusCounts.length" class="text-sm text-gray-500">Khong co du lieu du an.</div>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">
          Bao cao cham cong thang {{ attendanceMonthReport.month || '-' }}/{{ attendanceMonthReport.year || '-' }}
        </h3>
        <div class="mt-4 grid grid-cols-2 gap-3">
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Tong ban ghi</div>
            <div class="mt-1 text-xl font-semibold text-gray-900">{{ attendanceMonthReport.total_records || 0 }}</div>
          </div>
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Dung gio</div>
            <div class="mt-1 text-xl font-semibold text-emerald-700">{{ attendanceMonthReport.on_time_records || 0 }}</div>
          </div>
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Di muon</div>
            <div class="mt-1 text-xl font-semibold text-amber-700">{{ attendanceMonthReport.late_records || 0 }}</div>
          </div>
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Ve som</div>
            <div class="mt-1 text-xl font-semibold text-orange-700">{{ attendanceMonthReport.early_leave_records || 0 }}</div>
          </div>
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Vang mat</div>
            <div class="mt-1 text-xl font-semibold text-rose-700">{{ attendanceMonthReport.absent_records || 0 }}</div>
          </div>
          <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <div class="text-xs text-gray-500">Tong gio lam</div>
            <div class="mt-1 text-sm font-semibold text-gray-900">{{ formatMinutesToHours(attendanceMonthReport.total_worked_minutes) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <h3 class="text-lg font-semibold text-gray-900">Tien do tung du an dang trien khai</h3>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Du an</th>
              <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Trang thai</th>
              <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Tien do</th>
              <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500">Dau viec</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in activeProjectProgress" :key="item.id">
              <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ item.name }}</td>
              <td class="px-4 py-3 text-sm">
                <span :class="['inline-flex rounded-full px-2 py-1 text-xs font-semibold', projectStatusClass(item.status)]">
                  {{ item.status_label }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-700">
                <div class="w-44 rounded-full bg-gray-100">
                  <div
                    class="rounded-full bg-blue-600 px-2 py-0.5 text-right text-xs font-semibold text-white"
                    :style="{ width: `${item.progress_percent > 0 ? Math.max(8, item.progress_percent) : 0}%` }"
                  >
                    {{ item.progress_percent }}%
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ item.completed_tasks }}/{{ item.total_tasks }} hoan thanh</td>
            </tr>
            <tr v-if="!activeProjectProgress.length">
              <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Khong co du an dang trien khai.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <h3 class="text-lg font-semibold text-gray-900">Dieu huong nhanh</h3>
      <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
        <Link
          v-for="item in quickLinks"
          :key="item.path"
          :href="item.path"
          class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-400 hover:text-blue-600"
        >
          {{ item.label }}
        </Link>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const props = defineProps({
  stats: { type: Array, default: () => [] },
  warnings: { type: Array, default: () => [] },
  todayAttendance: { type: Object, default: null },
  dashboardSummary: { type: Object, default: () => ({}) },
})

const page = usePage()
const attendanceForm = useForm({})
const permissions = computed(() => page.props.auth?.permissions || {})
const positionCapabilities = computed(() => page.props.auth?.position_capabilities || {})
const isDepartmentHead = computed(() => !!page.props.auth?.user?.is_department_head)
const positionLabel = computed(() => page.props.auth?.user?.position_name || (page.props.auth?.user?.authority_level ? `Rank ${page.props.auth.user.authority_level}` : 'Chua thiet lap'))

const canCheckIn = computed(() => !props.todayAttendance?.check_in_at)
const canCheckOut = computed(() => !!props.todayAttendance?.check_in_at && !props.todayAttendance?.check_out_at)
const projectStatusCounts = computed(() => props.dashboardSummary?.project_status_counts || [])
const activeProjectProgress = computed(() => props.dashboardSummary?.active_project_progress || [])
const attendanceMonthReport = computed(() => props.dashboardSummary?.attendance_month_report || {})

const quickLinks = computed(() => {
  const links = []

  if (permissions.value['profile.view']) {
    links.push({ label: 'Ho so ca nhan', path: '/my-profile' })
  }

  if (permissions.value['attendance.mine.view']) {
    links.push({ label: 'Cong cua toi', path: '/my-attendance' })
  }

  links.push({ label: 'Bao cao tong hop', path: '/reports' })

  if (permissions.value['users.view']) {
    links.push({ label: 'Nhan su', path: '/users' })
  }

  if (permissions.value['attendance.manage.view']) {
    links.push({ label: 'Bao cao cham cong', path: '/attendance/reports' })
  }

  if (permissions.value['projects.all.view'] || positionCapabilities.value.manage_projects || isDepartmentHead.value) {
    links.push({ label: 'Du an', path: '/projects' })
  } else if (permissions.value['projects.mine.view']) {
    links.push({ label: 'Du an cua toi', path: '/my-projects' })
  }

  if (permissions.value['departments.view']) {
    links.push({ label: 'Phong ban', path: '/departments' })
  }

  return links
})

function checkIn() {
  attendanceForm.post(route('attendance.check-in'), { preserveScroll: true })
}

function checkOut() {
  attendanceForm.post(route('attendance.check-out'), { preserveScroll: true })
}

function formatDateTime(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('vi-VN')
}

function formatMinutesToHours(minutes) {
  const totalMinutes = Number(minutes || 0)
  const hours = Math.floor(totalMinutes / 60)
  const remainMinutes = totalMinutes % 60
  return `${hours} gio ${remainMinutes} phut`
}

function projectStatusClass(status) {
  switch (status) {
    case 'in_progress':
      return 'bg-blue-100 text-blue-700'
    case 'planning':
      return 'bg-slate-100 text-slate-700'
    case 'on_hold':
      return 'bg-amber-100 text-amber-700'
    case 'completed':
      return 'bg-emerald-100 text-emerald-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

function getStatusClass(status) {
  switch (status) {
    case 'on_time':
    case 'present':
      return 'bg-green-100 text-green-700'
    case 'late':
    case 'half_day':
      return 'bg-amber-100 text-amber-700'
    case 'absent':
    case 'leave':
      return 'bg-red-100 text-red-700'
    case 'pending':
      return 'bg-blue-100 text-blue-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}
</script>
