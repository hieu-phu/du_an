<template>
  <Head title="Dashboard" />

  <AdminLayout>
    <PageBreadcrumb title="Dashboard" :items="[{ text: 'Dashboard', link: null }]" />

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Tổng quan hệ thống</h2>
          <p class="mt-1 text-sm text-gray-500">
            Vai trò hiện tại: {{ roleLabel }}
          </p>
        </div>

        <div v-if="permissions['attendance.mine.action']" class="flex flex-wrap gap-3">
          <button
            v-if="canCheckIn"
            @click="checkIn"
            :disabled="attendanceForm.processing"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
          >
            {{ attendanceForm.processing ? 'Đang xử lý...' : 'Check in' }}
          </button>
          <button
            v-if="canCheckOut"
            @click="checkOut"
            :disabled="attendanceForm.processing"
            class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:opacity-60"
          >
            {{ attendanceForm.processing ? 'Đang xử lý...' : 'Check out' }}
          </button>
        </div>
      </div>

      <div v-if="todayAttendance" class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Ngày:</span>
            <span class="font-medium">{{ formatDate(todayAttendance.work_date) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Vào:</span>
            <span class="font-medium text-blue-600">{{ formatDateTime(todayAttendance.check_in_at) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Ra:</span>
            <span class="font-medium text-amber-600">{{ formatDateTime(todayAttendance.check_out_at) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">Trạng thái:</span>
            <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', getStatusClass(todayAttendance.status)]">
              {{ todayAttendance.status_label || 'Chưa chấm công' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Warnings Center -->
    <div v-if="warnings && warnings.length > 0" class="mb-6 overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm">
      <div class="flex items-center gap-2 border-b border-red-100 bg-red-50/50 px-4 py-3">
        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-red-600">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
        </div>
        <h3 class="text-sm font-bold text-red-900 uppercase tracking-wider">Thông báo quan trọng</h3>
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
          <div :class="[
            'flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl',
            warning.type === 'danger' ? 'bg-red-50 text-red-500' :
            warning.type === 'warning' ? 'bg-amber-50 text-amber-500' :
            warning.type === 'info' ? 'bg-blue-50 text-blue-500' :
            'bg-purple-50 text-purple-500'
          ]">
            <svg v-if="warning.type === 'danger' || warning.type === 'warning'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
          </div>
          
          <div class="min-w-0 flex-1">
            <h4 class="text-sm font-bold text-gray-900">{{ warning.title }}</h4>
            <p class="text-xs text-gray-500">{{ warning.message }}</p>
          </div>
          
          <div class="flex-shrink-0">
            <Link
              :href="warning.cta_url"
              :class="[
                'flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-bold transition-all',
                warning.type === 'danger' ? 'bg-red-50 text-red-600 hover:bg-red-100' :
                warning.type === 'warning' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' :
                warning.type === 'info' ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' :
                'bg-purple-50 text-purple-600 hover:bg-purple-100'
              ]"
            >
              {{ warning.cta_label }}
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
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

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <h3 class="text-lg font-semibold text-gray-900">Điều hướng nhanh</h3>
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
})

const page = usePage()
const attendanceForm = useForm({})
const permissions = computed(() => page.props.auth?.permissions || {})
const positionCapabilities = computed(() => page.props.auth?.position_capabilities || {})
const isDepartmentHead = computed(() => !!page.props.auth?.user?.is_department_head)
const primaryRole = computed(() => page.props.auth?.user?.primary_role || 'employee')

const roleLabel = computed(() => ({
  admin: 'Admin',
  hr: 'HR',
  employee: 'Nhân viên',
}[primaryRole.value] || primaryRole.value))

const canCheckIn = computed(() => !props.todayAttendance?.check_in_at)
const canCheckOut = computed(() => !!props.todayAttendance?.check_in_at && !props.todayAttendance?.check_out_at)

const quickLinks = computed(() => {
  const links = []

  if (permissions.value['profile.view']) {
    links.push({ label: 'Hồ sơ cá nhân', path: '/my-profile' })
  }

  if (permissions.value['attendance.mine.view']) {
    links.push({ label: 'Công của tôi', path: '/my-attendance' })
  }

  if (permissions.value['users.view']) {
    links.push({ label: 'Nhân sự', path: '/users' })
  }

  if (permissions.value['attendance.manage.view']) {
    links.push({ label: 'Báo cáo chấm công', path: '/attendance/reports' })
  }

  if (permissions.value['projects.all.view'] || positionCapabilities.value.manage_projects || isDepartmentHead.value) {
    links.push({ label: 'Dự án', path: '/projects' })
  } else if (permissions.value['projects.mine.view']) {
    links.push({ label: 'Dự án của tôi', path: '/my-projects' })
  }

  if (permissions.value['departments.view']) {
    links.push({ label: 'Phòng ban', path: '/departments' })
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

