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
        <div>Hôm nay: {{ formatDate(todayAttendance.work_date) }}</div>
        <div>Check in: {{ formatDateTime(todayAttendance.check_in_at) }}</div>
        <div>Check out: {{ formatDateTime(todayAttendance.check_out_at) }}</div>
        <div>Trạng thái: {{ todayAttendance.status_label || 'Chưa chấm công' }}</div>
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
  todayAttendance: { type: Object, default: null },
})

const page = usePage()
const attendanceForm = useForm({})
const permissions = computed(() => page.props.auth?.permissions || {})
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

  if (permissions.value['projects.all.view']) {
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
</script>
