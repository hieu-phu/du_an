<template>
  <AdminLayout title="Phan hoi noi bo">
    <PageBreadcrumb title="Phan hoi noi bo" :items="[{ text: 'Phan hoi noi bo', link: null }]" />

    <div class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-6">
      <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Da gui</div>
        <div class="mt-1 text-2xl font-bold text-gray-900">{{ summary.sent_total || 0 }}</div>
      </div>
      <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Da gui chua xu ly</div>
        <div class="mt-1 text-2xl font-bold text-amber-700">{{ summary.sent_unprocessed || 0 }}</div>
      </div>
      <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Da gui da xu ly</div>
        <div class="mt-1 text-2xl font-bold text-emerald-700">{{ summary.sent_processed || 0 }}</div>
      </div>
      <div v-if="canReply" class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Hop thu</div>
        <div class="mt-1 text-2xl font-bold text-gray-900">{{ summary.inbox_total || 0 }}</div>
      </div>
      <div v-if="canReply" class="rounded-xl border border-amber-200 bg-amber-50 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Hop thu chua xu ly</div>
        <div class="mt-1 text-2xl font-bold text-amber-700">{{ summary.inbox_unprocessed || 0 }}</div>
      </div>
      <div v-if="canReply" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Hop thu da xu ly</div>
        <div class="mt-1 text-2xl font-bold text-emerald-700">{{ summary.inbox_processed || 0 }}</div>
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
      <h3 class="mb-4 text-base font-semibold text-gray-900">Bo loc quan ly phan hoi</h3>
      <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Trang thai tin</label>
          <select v-model="filterForm.status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Tat ca</option>
            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Trang thai xu ly</label>
          <select v-model="filterForm.processing_state" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Tat ca</option>
            <option v-for="option in processingOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button @click="applyFilters" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Loc du lieu</button>
          <button @click="resetFilters" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Bo loc</button>
        </div>
      </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
      <h3 class="mb-4 text-base font-semibold text-gray-900">Gui phan hoi / de xuat</h3>
      <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Gui toi</label>
          <select v-model="form.receiver_group" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="hr">HR</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="md:col-span-3">
          <label class="mb-1 block text-sm font-medium text-gray-700">Tieu de</label>
          <input v-model="form.subject" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Nhap tieu de phan hoi" />
        </div>
        <div class="md:col-span-4">
          <label class="mb-1 block text-sm font-medium text-gray-700">Noi dung</label>
          <textarea v-model="form.message" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Nhap noi dung chi tiet"></textarea>
        </div>
      </div>
      <div class="mt-3">
        <button :disabled="form.processing" @click="submitFeedback" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
          {{ form.processing ? 'Dang gui...' : 'Gui phan hoi' }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
      <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="mb-3 text-base font-semibold text-gray-900">Phan hoi da gui</h3>
        <div class="space-y-3">
          <div v-for="item in sent" :key="`sent-${item.id}`" class="rounded-lg border border-gray-200 p-3">
            <div class="flex items-start justify-between gap-2">
              <div>
                <div class="font-semibold text-gray-900">{{ item.subject }}</div>
                <div class="text-xs text-gray-500">Gui toi: {{ item.receiver_group_label }} | {{ formatDateTime(item.created_at) }}</div>
              </div>
              <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">{{ item.status_label }}</span>
            </div>
            <div class="mt-2 text-sm text-gray-700">{{ item.message }}</div>
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
              <span class="rounded-full bg-gray-100 px-2 py-1 font-semibold text-gray-700">Xu ly: {{ item.processing_state_label }}</span>
            </div>
            <div v-if="item.reply_history?.length" class="mt-3 rounded-lg border border-emerald-200 bg-emerald-50 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-emerald-800">Lich su tra loi</div>
              <div v-for="history in item.reply_history" :key="`sent-history-${history.id}`" class="mb-2 rounded border border-emerald-100 bg-white p-2 last:mb-0">
                <div class="text-xs font-semibold text-emerald-800">
                  {{ history.replier?.name || '-' }} - {{ formatDateTime(history.created_at) }}
                </div>
                <div class="mt-1 text-sm text-emerald-900">{{ history.message }}</div>
              </div>
            </div>
          </div>
          <div v-if="!sent.length" class="rounded-lg border border-dashed border-gray-300 px-3 py-4 text-sm text-gray-500">Chua co phan hoi nao.</div>
        </div>
      </div>

      <div v-if="canReply" class="rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="mb-3 text-base font-semibold text-gray-900">Hop thu can xu ly</h3>
        <div class="space-y-3">
          <div v-for="item in inbox" :key="`inbox-${item.id}`" class="rounded-lg border border-gray-200 p-3">
            <div class="flex items-start justify-between gap-2">
              <div>
                <div class="font-semibold text-gray-900">{{ item.subject }}</div>
                <div class="text-xs text-gray-500">Nguoi gui: {{ item.sender?.name || '-' }} | {{ formatDateTime(item.created_at) }}</div>
              </div>
              <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">{{ item.status_label }}</span>
            </div>
            <div class="mt-2 text-sm text-gray-700">{{ item.message }}</div>

            <div class="mt-3 flex flex-wrap gap-2">
              <button @click="markRead(item)" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700">Danh dau da doc</button>
              <button @click="openReply(item)" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white">Tra loi</button>
              <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">Xu ly: {{ item.processing_state_label }}</span>
            </div>

            <div v-if="item.reply_history?.length" class="mt-3 rounded-lg border border-emerald-200 bg-emerald-50 p-3">
              <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-emerald-800">Lich su tra loi</div>
              <div v-for="history in item.reply_history" :key="`inbox-history-${history.id}`" class="mb-2 rounded border border-emerald-100 bg-white p-2 last:mb-0">
                <div class="text-xs font-semibold text-emerald-800">
                  {{ history.replier?.name || '-' }} - {{ formatDateTime(history.created_at) }}
                </div>
                <div class="mt-1 text-sm text-emerald-900">{{ history.message }}</div>
              </div>
            </div>
          </div>
          <div v-if="!inbox.length" class="rounded-lg border border-dashed border-gray-300 px-3 py-4 text-sm text-gray-500">Khong co phan hoi nao can xu ly.</div>
        </div>
      </div>
    </div>

    <div v-if="canReply" class="mt-4 rounded-xl border border-gray-200 bg-white p-5">
      <h3 class="mb-3 text-base font-semibold text-gray-900">Log gui mail phan hoi</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase tracking-wide text-gray-500">
              <th class="px-2 py-2">Thoi gian</th>
              <th class="px-2 py-2">Nguoi gui</th>
              <th class="px-2 py-2">Nguoi nhan</th>
              <th class="px-2 py-2">Tieu de</th>
              <th class="px-2 py-2">Trang thai</th>
              <th class="px-2 py-2">Loi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in mailLogs" :key="`mail-log-${log.id}`" class="border-b border-gray-100 align-top last:border-b-0">
              <td class="px-2 py-2 text-gray-700">{{ formatDateTime(log.sent_at) }}</td>
              <td class="px-2 py-2 text-gray-700">{{ log.sender?.name || '-' }}</td>
              <td class="px-2 py-2 text-gray-700">{{ log.receiver_email }}</td>
              <td class="px-2 py-2 text-gray-700">{{ log.subject }}</td>
              <td class="px-2 py-2">
                <span :class="log.status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'" class="rounded-full px-2 py-0.5 text-xs font-semibold">
                  {{ log.status }}
                </span>
              </td>
              <td class="px-2 py-2 text-xs text-rose-700">{{ log.error_message || '-' }}</td>
            </tr>
            <tr v-if="!mailLogs.length">
              <td colspan="6" class="px-2 py-4 text-center text-sm text-gray-500">Chua co log mail phan hoi.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <CustomModal
      v-if="replyTarget"
      title="Tra loi phan hoi"
      @close="closeReply"
      :custom_class="['relative', 'w-full', 'max-w-[680px]', 'rounded-xl', 'bg-white', 'shadow-2xl']"
    >
      <template #body>
        <div class="p-5">
          <div class="mb-3 text-sm text-gray-700">
            <div><span class="font-semibold text-gray-900">Tieu de:</span> {{ replyTarget.subject }}</div>
            <div><span class="font-semibold text-gray-900">Nguoi gui:</span> {{ replyTarget.sender?.name || '-' }}</div>
          </div>
          <textarea v-model="replyForm.reply_message" rows="5" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Nhap noi dung tra loi"></textarea>
          <div class="mt-3 flex justify-end gap-2">
            <button @click="closeReply" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Dong</button>
            <button :disabled="replyForm.processing" @click="submitReply" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">
              {{ replyForm.processing ? 'Dang gui...' : 'Gui tra loi' }}
            </button>
          </div>
        </div>
      </template>
    </CustomModal>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import CustomModal from '@/components/modals/CustomModal.vue'

const props = defineProps({
  sent: { type: Array, default: () => [] },
  inbox: { type: Array, default: () => [] },
  mailLogs: { type: Array, default: () => [] },
  canReply: { type: Boolean, default: false },
  filters: { type: Object, default: () => ({}) },
  statusOptions: { type: Array, default: () => [] },
  processingOptions: { type: Array, default: () => [] },
  summary: { type: Object, default: () => ({}) },
})

const form = useForm({
  receiver_group: 'hr',
  subject: '',
  message: '',
})

const replyTarget = ref(null)
const replyForm = useForm({
  reply_message: '',
  status: 'read',
})
const filterForm = useForm({
  status: props.filters?.status || '',
  processing_state: props.filters?.processing_state || '',
})

const submitFeedback = () => {
  form.post(route('feedbacks.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('subject', 'message')
      toast.success('Da gui phan hoi.')
    },
    onError: () => toast.error('Khong the gui phan hoi.'),
  })
}

const markRead = (item) => {
  router.post(route('feedbacks.read', item.id), {}, {
    preserveScroll: true,
  })
}

const openReply = (item) => {
  replyTarget.value = item
  replyForm.reset()
}

const closeReply = () => {
  replyTarget.value = null
  replyForm.reset()
}

const submitReply = () => {
  if (!replyTarget.value) return

  replyForm.post(route('feedbacks.reply', replyTarget.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeReply()
      toast.success('Da gui tra loi.')
    },
    onError: () => toast.error('Khong the gui tra loi.'),
  })
}

const applyFilters = () => {
  router.get(route('feedbacks.index'), {
    status: filterForm.status || undefined,
    processing_state: filterForm.processing_state || undefined,
  }, {
    preserveScroll: true,
    replace: true,
  })
}

const resetFilters = () => {
  filterForm.status = ''
  filterForm.processing_state = ''
  applyFilters()
}

const formatDateTime = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('vi-VN')
}
</script>
