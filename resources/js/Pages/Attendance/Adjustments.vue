<template>
  <Head title="Điều chỉnh công" />

  <AdminLayout>
    <PageBreadcrumb title="Điều chỉnh công" :items="[{ text: 'Chấm công', link: null }, { text: 'Điều chỉnh công', link: null }]" />

    <div class="space-y-6">
      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Áp dụng điều chỉnh công</h3>
        <p class="mt-1 text-sm text-gray-500">Điều chỉnh được áp dụng ngay lên bản ghi công chưa duyệt. Bản ghi sau đó vẫn nằm trong luồng duyệt công chung nếu cần.</p>

        <div v-if="formError" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          {{ formError }}
        </div>

        <form class="mt-4 space-y-5" @submit.prevent="submit">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Bản ghi cần điều chỉnh</label>
            <select v-model="form.attendance_record_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
              <option :value="null">Chọn ngày công</option>
              <option v-for="item in records" :key="item.id" :value="item.id">
                {{ formatDate(item.work_date) }} - {{ item.status_label || formatWorkResult(item) }} - {{ item.approval_status_label || formatApprovalStatus(item.approval_status) }}
              </option>
            </select>
            <p v-if="form.errors.attendance_record_id" class="mt-1 text-sm text-red-500">{{ form.errors.attendance_record_id }}</p>
          </div>

          <div v-if="selectedRecord" class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
              <h4 class="text-sm font-semibold text-gray-900">Bản ghi hiện tại</h4>
              <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Ngày công</dt>
                  <dd class="font-medium text-gray-900">{{ formatDate(selectedRecord.work_date) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Check-in hiện tại</dt>
                  <dd class="font-medium text-gray-900">{{ formatDateTime(selectedRecord.check_in_at) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Check-out hiện tại</dt>
                  <dd class="font-medium text-gray-900">{{ formatDateTime(selectedRecord.check_out_at) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Giờ làm hiện tại</dt>
                  <dd class="font-medium text-gray-900">{{ formatMinutes(selectedRecord.worked_minutes) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Trạng thái ngày</dt>
                  <dd class="font-medium text-gray-900">{{ selectedRecord.day_status_label || formatDayStatus(selectedRecord.day_status) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Duyệt hiện tại</dt>
                  <dd class="font-medium text-gray-900">{{ selectedRecord.approval_status_label || formatApprovalStatus(selectedRecord.approval_status) }}</dd>
                </div>
              </dl>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 lg:col-span-2">
              <h4 class="text-sm font-semibold text-gray-900">Nội dung đề xuất</h4>
              <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                <InputDate
                  v-model="form.new_check_in_at"
                  label="Check-in đề xuất"
                  placeholder="Chọn ngày và giờ check-in"
                  :disabled="checkInDisabled"
                  :error="form.errors.new_check_in_at"
                  :config="dateTimePickerConfig"
                />
                <InputDate
                  v-model="form.new_check_out_at"
                  label="Check-out đề xuất"
                  placeholder="Chọn ngày và giờ check-out"
                  :disabled="checkOutDisabled"
                  :error="form.errors.new_check_out_at"
                  :config="dateTimePickerConfig"
                />
              </div>

              <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
                <div class="font-semibold">Kết quả dự kiến</div>
                <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-4">
                  <div>
                    <div class="text-blue-700">Check-in sau điều chỉnh</div>
                    <div class="font-medium">{{ formatDateTime(resolvedCheckIn) }}</div>
                  </div>
                  <div>
                    <div class="text-blue-700">Check-out sau điều chỉnh</div>
                    <div class="font-medium">{{ formatDateTime(resolvedCheckOut) }}</div>
                  </div>
                  <div>
                    <div class="text-blue-700">Giờ làm dự kiến</div>
                    <div class="font-medium">{{ formatMinutes(previewWorkedMinutes) }}</div>
                  </div>
                  <div>
                    <div class="text-blue-700">Trạng thái</div>
                    <div class="font-medium">{{ previewStatus }}</div>
                  </div>
                </div>
              </div>

              <div v-if="logicMessages.length" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <div class="font-semibold">Ràng buộc điều chỉnh</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                  <li v-for="message in logicMessages" :key="message">{{ message }}</li>
                </ul>
              </div>
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Lý do điều chỉnh</label>
            <textarea v-model="form.reason" class="w-full rounded-lg border border-gray-300 px-3 py-2" rows="3" placeholder="Nhập lý do điều chỉnh cụ thể" />
            <p v-if="form.errors.reason" class="mt-1 text-sm text-red-500">{{ form.errors.reason }}</p>
          </div>

          <div class="flex justify-end">
            <button
              class="rounded-lg bg-blue-600 px-4 py-2 text-white disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="form.processing || !canSubmit"
              type="submit"
            >
              {{ form.processing ? 'Đang áp dụng...' : 'Áp dụng điều chỉnh' }}
            </button>
          </div>
        </form>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Lịch sử điều chỉnh</h3>
        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Ngày công</th>
                <th class="p-2">Check-in cũ / mới</th>
                <th class="p-2">Check-out cũ / mới</th>
                <th class="p-2">Sau điều chỉnh</th>
                <th class="p-2">Trạng thái</th>
                <th class="p-2">Lý do</th>
                <th class="p-2">Ghi chú</th>
                <th class="p-2">Thời gian</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in adjustments" :key="item.id" class="border-t">
                <td class="p-2">{{ formatDate(item.work_date) }}</td>
                <td class="p-2">{{ formatAdjustmentChange(item.old_check_in_at, item.new_check_in_at) }}</td>
                <td class="p-2">{{ formatAdjustmentChange(item.old_check_out_at, item.new_check_out_at) }}</td>
                <td class="p-2">
                  <div>Vào: {{ formatDateTime(finalAdjustmentCheckIn(item)) }}</div>
                  <div>Ra: {{ formatDateTime(finalAdjustmentCheckOut(item)) }}</div>
                </td>
                <td class="p-2">
                  <span :class="statusClass(item.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
                    {{ item.status_label || formatApprovalStatus(item.status) }}
                  </span>
                </td>
                <td class="p-2">{{ item.reason || '-' }}</td>
                <td class="p-2">{{ item.review_note || '-' }}</td>
                <td class="p-2">{{ formatDateTime(item.submitted_at) }}</td>
              </tr>
              <tr v-if="!adjustments.length">
                <td class="p-4 text-center text-gray-500" colspan="8">Chưa có lần điều chỉnh nào.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import InputDate from '@/components/forms/InputDate.vue'

const props = defineProps({
  records: { type: Array, default: () => [] },
  adjustments: { type: Array, default: () => [] },
})

const page = usePage()

const form = useForm({
  attendance_record_id: null,
  new_check_in_at: '',
  new_check_out_at: '',
  reason: '',
})

const dateTimePickerConfig = {
  allowInput: false,
  enableTime: true,
  time_24hr: true,
  minuteIncrement: 5,
  dateFormat: 'Y-m-d H:i',
  altInput: true,
  altFormat: 'd/m/Y H:i',
}

const selectedRecord = computed(() => props.records.find((item) => Number(item.id) === Number(form.attendance_record_id)) || null)
const isApprovedRecord = computed(() => selectedRecord.value?.is_confirmed || selectedRecord.value?.approval_status === 'approved')
const checkInMissing = computed(() => Boolean(selectedRecord.value?.missing_check_in) || selectedRecord.value?.day_status === 'missing_check_in' || !selectedRecord.value?.check_in_at)
const checkOutMissing = computed(() => Boolean(selectedRecord.value?.missing_check_out) || selectedRecord.value?.day_status === 'missing_check_out' || !selectedRecord.value?.check_out_at)
const checkInDisabled = computed(() => !selectedRecord.value || isApprovedRecord.value || (checkOutMissing.value && !checkInMissing.value))
const checkOutDisabled = computed(() => !selectedRecord.value || isApprovedRecord.value || (checkInMissing.value && !checkOutMissing.value))
const proposedCheckIn = computed(() => normalizeDateTimeForForm(form.new_check_in_at))
const proposedCheckOut = computed(() => normalizeDateTimeForForm(form.new_check_out_at))
const currentCheckIn = computed(() => normalizeDateTimeForForm(selectedRecord.value?.check_in_at))
const currentCheckOut = computed(() => normalizeDateTimeForForm(selectedRecord.value?.check_out_at))
const mergedAdjustment = computed(() => ({
  checkIn: proposedCheckIn.value || currentCheckIn.value,
  checkOut: proposedCheckOut.value || currentCheckOut.value,
}))
const resolvedCheckIn = computed(() => mergedAdjustment.value.checkIn)
const resolvedCheckOut = computed(() => mergedAdjustment.value.checkOut)
const formError = computed(() => form.errors.error || page.props.errors?.error || '')

const previewWorkedMinutes = computed(() => {
  if (!resolvedCheckIn.value || !resolvedCheckOut.value) return 0
  const start = parseLocalDateTime(resolvedCheckIn.value)
  const end = parseLocalDateTime(resolvedCheckOut.value)
  if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end <= start) return 0
  return Math.floor((end.getTime() - start.getTime()) / 60000)
})

const previewStatus = computed(() => {
  if (!selectedRecord.value) return '-'
  if (!resolvedCheckIn.value) return 'Thiếu check in'
  if (!resolvedCheckOut.value) return 'Thiếu check out'
  if (!isCheckoutAfterCheckin.value) return 'Giờ không hợp lệ'
  if (previewWorkedMinutes.value >= 480) return 'Đủ công'
  if (previewWorkedMinutes.value >= 240) return 'Nửa công'
  return 'Không công'
})

const workDateMatches = computed(() => {
  if (!selectedRecord.value) return false
  const workDate = normalizeDateOnly(selectedRecord.value.work_date)
  const values = [proposedCheckIn.value, proposedCheckOut.value].filter(Boolean)
  return values.every((value) => normalizeDateOnly(value) === workDate)
})

const isCheckoutAfterCheckin = computed(() => {
  if (!resolvedCheckIn.value || !resolvedCheckOut.value) return true
  const start = parseLocalDateTime(resolvedCheckIn.value)
  const end = parseLocalDateTime(resolvedCheckOut.value)
  return !Number.isNaN(start.getTime()) && !Number.isNaN(end.getTime()) && end > start
})

const hasActualChange = computed(() => {
  if (!selectedRecord.value) return false
  return Boolean(proposedCheckIn.value && !sameMinute(proposedCheckIn.value, currentCheckIn.value))
    || Boolean(proposedCheckOut.value && !sameMinute(proposedCheckOut.value, currentCheckOut.value))
})

const logicMessages = computed(() => {
  const messages = []
  if (!selectedRecord.value) {
    messages.push('Chọn bản ghi công cần điều chỉnh.')
    return messages
  }
  if (isApprovedRecord.value) messages.push('Bản ghi đã được duyệt, nhân viên không thể tự gửi điều chỉnh.')
  if (!workDateMatches.value && (form.new_check_in_at || form.new_check_out_at)) messages.push('Thời gian đề xuất phải nằm trong đúng ngày công đang chọn.')
  if (!isCheckoutAfterCheckin.value) messages.push('Check-out đề xuất phải sau check-in đề xuất.')
  if (!hasActualChange.value) messages.push('Nhập ít nhất một thời gian đề xuất khác dữ liệu hiện tại.')
  if (checkInDisabled.value && !checkOutDisabled.value) messages.push('Ngày này đang thiếu check-out, chỉ cần bổ sung check-out đề xuất.')
  if (checkOutDisabled.value && !checkInDisabled.value) messages.push('Ngày này đang thiếu check-in, chỉ cần bổ sung check-in đề xuất.')
  return messages
})

const canSubmit = computed(() => selectedRecord.value
  && !isApprovedRecord.value
  && workDateMatches.value
  && isCheckoutAfterCheckin.value
  && hasActualChange.value
  && String(form.reason || '').trim().length > 0)

watch(() => form.attendance_record_id, () => {
  form.clearErrors()
  form.new_check_in_at = ''
  form.new_check_out_at = ''
})

const submit = () => {
  form.clearErrors()
  if (!canSubmit.value) {
    applyClientValidationErrors()
    return
  }

  form.new_check_in_at = proposedCheckIn.value
  form.new_check_out_at = proposedCheckOut.value

  form.post(route('attendance.adjustments.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

function normalizeDateValue(value) {
  if (!value) return ''
  if (value instanceof Date) {
    return formatLocalDateTime(value)
  }
  return String(value).replace(' ', 'T')
}

function normalizeDateTimeForForm(value) {
  if (!value) return ''
  if (value instanceof Date) {
    return formatLocalDateTime(value)
  }

  const text = String(value).trim()
  if (!text) return ''

  const match = text.match(/^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2})(?::\d{2})?/)
  if (match) {
    return `${match[1]} ${match[2]}`
  }

  const parsed = new Date(text.replace(' ', 'T'))
  return Number.isNaN(parsed.getTime()) ? text : formatLocalDateTime(parsed)
}

function normalizeDateOnly(value) {
  const normalized = normalizeDateTimeForForm(value)
  if (normalized) return normalized.slice(0, 10)
  if (!value) return ''
  return String(value).slice(0, 10)
}

function parseLocalDateTime(value) {
  return new Date(normalizeDateValue(value))
}

function formatLocalDateTime(date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${year}-${month}-${day} ${hours}:${minutes}`
}

function sameMinute(left, right) {
  if (!left || !right) return false
  const leftDate = parseLocalDateTime(left)
  const rightDate = parseLocalDateTime(right)
  if (Number.isNaN(leftDate.getTime()) || Number.isNaN(rightDate.getTime())) return false
  leftDate.setSeconds(0, 0)
  rightDate.setSeconds(0, 0)
  return leftDate.getTime() === rightDate.getTime()
}

function formatDate(value) {
  if (!value) return '-'
  const date = parseLocalDateTime(value)
  return Number.isNaN(date.getTime()) ? '-' : date.toLocaleDateString('vi-VN')
}

function formatDateTime(value) {
  if (!value) return '-'
  const date = parseLocalDateTime(value)
  return Number.isNaN(date.getTime()) ? '-' : date.toLocaleString('vi-VN')
}

function formatAdjustmentChange(oldValue, newValue) {
  const oldText = formatDateTime(oldValue)
  if (!newValue) {
    return `${oldText} -> Giữ nguyên`
  }

  return `${oldText} -> ${formatDateTime(newValue)}`
}

function finalAdjustmentCheckIn(item) {
  return item?.new_check_in_at || item?.old_check_in_at || ''
}

function finalAdjustmentCheckOut(item) {
  return item?.new_check_out_at || item?.old_check_out_at || ''
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

function formatWorkResult(item) {
  if (item?.missing_check_in || item?.missing_check_out) return 'Chưa tính công'
  const unit = Number(item?.work_unit)
  if (unit >= 1) return 'Đủ công'
  if (unit >= 0.5) return 'Nửa công'
  return 'Không công'
}

function formatDayStatus(value) {
  const labels = {
    present: 'Đi làm',
    late: 'Đi muộn',
    early_leave: 'Về sớm',
    leave: 'Nghỉ phép',
    unpaid_leave: 'Nghỉ không phép',
    business_trip: 'Công tác',
    missing_check_in: 'Thiếu check-in',
    missing_check_out: 'Thiếu check-out',
    absent: 'Vắng',
  }
  return labels[value] || '-'
}

function formatApprovalStatus(value) {
  const labels = {
    pending: 'Chờ xử lý cũ',
    approved: 'Đã áp dụng',
    rejected: 'Từ chối',
  }
  return labels[value] || '-'
}

function statusClass(value) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700',
    approved: 'bg-emerald-50 text-emerald-700',
    rejected: 'bg-rose-50 text-rose-700',
  }
  return classes[value] || 'bg-slate-50 text-slate-700'
}

function applyClientValidationErrors() {
  if (!selectedRecord.value) {
    form.setError('attendance_record_id', 'Chọn bản ghi công cần điều chỉnh.')
    return
  }

  if (isApprovedRecord.value) {
    form.setError('attendance_record_id', 'Bản ghi đã được duyệt, vui lòng liên hệ HR để điều chỉnh.')
  }

  if (!workDateMatches.value) {
    form.setError('new_check_in_at', 'Thời gian đề xuất phải nằm trong đúng ngày công đang chọn.')
    form.setError('new_check_out_at', 'Thời gian đề xuất phải nằm trong đúng ngày công đang chọn.')
  }

  if (!isCheckoutAfterCheckin.value) {
    form.setError('new_check_out_at', 'Check-out đề xuất phải sau check-in sau điều chỉnh.')
  }

  if (!hasActualChange.value) {
    form.setError('new_check_in_at', 'Nhập thời gian đề xuất khác dữ liệu hiện tại.')
    form.setError('new_check_out_at', 'Nhập thời gian đề xuất khác dữ liệu hiện tại.')
  }

  if (!String(form.reason || '').trim()) {
    form.setError('reason', 'Lý do điều chỉnh là bắt buộc.')
  }
}
</script>
