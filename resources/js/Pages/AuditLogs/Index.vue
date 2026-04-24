<template>
  <AdminLayout title="Truy vết hoạt động">
    <PageBreadcrumb
      title="Truy vết hoạt động"
      :items="[
        { text: 'Báo cáo', link: null },
        { text: 'Truy vết hoạt động', link: null },
      ]"
    />

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="grid grid-cols-1 gap-4 xl:grid-cols-6">
        <div class="xl:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700">Tìm kiếm</label>
          <input
            v-model="form.search"
            type="text"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
            placeholder="Tên, email, IP, thiết bị..."
            @keyup.enter="applyFilters"
          />
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700">Tài khoản</label>
          <select v-model="form.user_id" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500">
            <option value="">Tất cả</option>
            <option v-for="user in users" :key="user.id" :value="String(user.id)">
              {{ user.label }}
            </option>
          </select>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700">Module</label>
          <select v-model="form.module" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500">
            <option value="">Tất cả</option>
            <option v-for="module in modules" :key="module" :value="module">
              {{ module }}
            </option>
          </select>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700">Hành động</label>
          <select v-model="form.action" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500">
            <option value="">Tất cả</option>
            <option v-for="action in actions" :key="action" :value="action">
              {{ action }}
            </option>
          </select>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700">Số dòng</label>
          <select v-model="form.per_page" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500">
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700">Từ ngày</label>
          <input v-model="form.date_from" type="date" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500" />
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700">Đến ngày</label>
          <input v-model="form.date_to" type="date" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500" />
        </div>

        <div class="xl:col-span-4 flex flex-wrap items-end gap-3">
          <button
            type="button"
            class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
            @click="applyFilters"
          >
            Lọc dữ liệu
          </button>

          <button
            type="button"
            class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            @click="resetFilters"
          >
            Xóa lọc
          </button>
        </div>
      </div>
    </div>

    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
      <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-lg font-semibold text-gray-900">Nhật ký hoạt động</h3>
        <p class="mt-1 text-sm text-gray-500">Theo dõi tài khoản đã thực hiện hành động gì, ở đâu, bằng thiết bị nào và vào lúc nào.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tài khoản</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Hành động</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Mô tả</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">IP / Thiết bị</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thời gian</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="log in activityLogs.data" :key="log.id" class="align-top">
              <td class="px-4 py-4 text-sm text-gray-700">
                <div class="font-medium text-gray-900">{{ log.user_name || 'Hệ thống' }}</div>
                <div class="text-xs text-gray-500">{{ log.user_email || '-' }}</div>
              </td>
              <td class="px-4 py-4 text-sm text-gray-700">
                <div class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ log.action }}</div>
                <div class="mt-2 text-xs text-gray-500">{{ log.module }}</div>
                <div v-if="log.reference_table" class="mt-1 text-xs text-gray-500">
                  {{ log.reference_table }}#{{ log.reference_id || '-' }}
                </div>
              </td>
              <td class="px-4 py-4 text-sm text-gray-700">
                <div class="max-w-xl whitespace-pre-line">{{ log.description || '-' }}</div>
                <div v-if="log.user_agent" class="mt-2 line-clamp-2 text-xs text-gray-500">{{ log.user_agent }}</div>
              </td>
              <td class="px-4 py-4 text-sm text-gray-700">
                <div>{{ log.ip_address || '-' }}</div>
                <div class="mt-1 text-xs text-gray-500">{{ log.device || '-' }}</div>
              </td>
              <td class="px-4 py-4 text-sm text-gray-700">{{ formatDateTime(log.occurred_at) }}</td>
            </tr>
            <tr v-if="!activityLogs.data.length">
              <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                Không có bản ghi truy vết phù hợp.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex flex-col gap-3 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-500">
          Hiển thị {{ activityLogs.from || 0 }}-{{ activityLogs.to || 0 }} / {{ activityLogs.total }} bản ghi
        </div>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!activityLogs.prev_page_url"
            @click="goToPage(activityLogs.current_page - 1)"
          >
            Trước
          </button>
          <div class="text-sm text-gray-600">Trang {{ activityLogs.current_page }} / {{ activityLogs.last_page }}</div>
          <button
            type="button"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!activityLogs.next_page_url"
            @click="goToPage(activityLogs.current_page + 1)"
          >
            Sau
          </button>
        </div>
      </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
      <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-lg font-semibold text-gray-900">Phiên đăng nhập gần đây</h3>
        <p class="mt-1 text-sm text-gray-500">Thông tin đăng nhập, đăng xuất, IP, thiết bị và trình duyệt.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tài khoản</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Đăng nhập</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Đăng xuất</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">IP</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thiết bị / Browser</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="history in loginHistories" :key="history.id">
              <td class="px-4 py-4 text-sm text-gray-700">
                <div class="font-medium text-gray-900">{{ history.user_name || '-' }}</div>
                <div class="text-xs text-gray-500">{{ history.user_email || '-' }}</div>
              </td>
              <td class="px-4 py-4 text-sm text-gray-700">{{ formatDateTime(history.login_at) }}</td>
              <td class="px-4 py-4 text-sm text-gray-700">{{ formatDateTime(history.logout_at) }}</td>
              <td class="px-4 py-4 text-sm text-gray-700">{{ history.ip_address || '-' }}</td>
              <td class="px-4 py-4 text-sm text-gray-700">
                <div>{{ history.device || '-' }}</div>
                <div class="text-xs text-gray-500">{{ history.browser || '-' }}</div>
              </td>
            </tr>
            <tr v-if="!loginHistories.length">
              <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                Chưa có lịch sử đăng nhập.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  activityLogs: { type: Object, required: true },
  loginHistories: { type: Array, default: () => [] },
  users: { type: Array, default: () => [] },
  modules: { type: Array, default: () => [] },
  actions: { type: Array, default: () => [] },
})

const form = reactive({
  search: props.filters?.search ?? '',
  user_id: props.filters?.user_id ?? '',
  module: props.filters?.module ?? '',
  action: props.filters?.action ?? '',
  date_from: props.filters?.date_from ?? '',
  date_to: props.filters?.date_to ?? '',
  per_page: String(props.filters?.per_page ?? 20),
})

const applyFilters = () => {
  router.get(route('activity-logs.index'), sanitizePayload({ ...form, page: 1 }), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const resetFilters = () => {
  form.search = ''
  form.user_id = ''
  form.module = ''
  form.action = ''
  form.date_from = ''
  form.date_to = ''
  form.per_page = '20'
  applyFilters()
}

const goToPage = (page) => {
  router.get(route('activity-logs.index'), sanitizePayload({ ...form, page }), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const sanitizePayload = (payload) => Object.fromEntries(
  Object.entries(payload).filter(([, value]) => value !== '' && value !== null && value !== undefined)
)

const formatDateTime = (value) => value || '-'
</script>
