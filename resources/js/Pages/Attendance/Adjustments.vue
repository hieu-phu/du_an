<template>
  <Head title="Dieu chinh cong" />

  <AdminLayout>
    <PageBreadcrumb title="Dieu chinh cong" :items="[{ text: 'Cham cong', link: null }, { text: 'Dieu chinh cong', link: null }]" />

    <div class="space-y-6">
      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Gui yeu cau dieu chinh</h3>

        <div v-if="formError" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          {{ formError }}
        </div>

        <form class="mt-4 space-y-5" @submit.prevent="submit">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Ban ghi can dieu chinh</label>
            <select v-model="form.attendance_record_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
              <option :value="null">Chon ngay cong</option>
              <option v-for="item in records" :key="item.id" :value="item.id">
                {{ formatDate(item.work_date) }} - {{ item.status_label || formatWorkResult(item) }} - {{ item.approval_status_label || formatApprovalStatus(item.approval_status) }}
              </option>
            </select>
            <p v-if="form.errors.attendance_record_id" class="mt-1 text-sm text-red-500">{{ form.errors.attendance_record_id }}</p>
          </div>

          <div v-if="selectedRecord" class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
              <h4 class="text-sm font-semibold text-gray-900">Ban ghi hien tai</h4>
              <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Ngay cong</dt>
                  <dd class="font-medium text-gray-900">{{ formatDate(selectedRecord.work_date) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Check-in hien tai</dt>
                  <dd class="font-medium text-gray-900">{{ formatDateTime(selectedRecord.check_in_at) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Check-out hien tai</dt>
                  <dd class="font-medium text-gray-900">{{ formatDateTime(selectedRecord.check_out_at) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Gio lam hien tai</dt>
                  <dd class="font-medium text-gray-900">{{ formatMinutes(selectedRecord.worked_minutes) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Trang thai ngay</dt>
                  <dd class="font-medium text-gray-900">{{ selectedRecord.day_status_label || formatDayStatus(selectedRecord.day_status) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt class="text-gray-500">Duyet hien tai</dt>
                  <dd class="font-medium text-gray-900">{{ selectedRecord.approval_status_label || formatApprovalStatus(selectedRecord.approval_status) }}</dd>
                </div>
              </dl>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 lg:col-span-2">
              <h4 class="text-sm font-semibold text-gray-900">Noi dung de xuat</h4>
              <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                <InputDate
                  v-model="form.new_check_in_at"
                  label="Check-in de xuat"
                  placeholder="Chon ngay va gio check-in"
                  :disabled="checkInDisabled"
                  :error="form.errors.new_check_in_at"
                  :config="dateTimePickerConfig"
                />
                <InputDate
                  v-model="form.new_check_out_at"
                  label="Check-out de xuat"
                  placeholder="Chon ngay va gio check-out"
                  :disabled="checkOutDisabled"
                  :error="form.errors.new_check_out_at"
                  :config="dateTimePickerConfig"
                />
              </div>

              <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
                <div class="font-semibold">Ket qua du kien</div>
                <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-4">
                  <div>
                    <div class="text-blue-700">Check-in sau dieu chinh</div>
                    <div class="font-medium">{{ formatDateTime(resolvedCheckIn) }}</div>
                  </div>
                  <div>
                    <div class="text-blue-700">Check-out sau dieu chinh</div>
                    <div class="font-medium">{{ formatDateTime(resolvedCheckOut) }}</div>
                  </div>
                  <div>
                    <div class="text-blue-700">Gio lam du kien</div>
                    <div class="font-medium">{{ formatMinutes(previewWorkedMinutes) }}</div>
                  </div>
                  <div>
                    <div class="text-blue-700">Trang thai</div>
                    <div class="font-medium">{{ previewStatus }}</div>
                  </div>
                </div>
              </div>

              <div v-if="logicMessages.length" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <div class="font-semibold">Rang buoc dieu chinh</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                  <li v-for="message in logicMessages" :key="message">{{ message }}</li>
                </ul>
              </div>
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Ly do dieu chinh</label>
            <textarea v-model="form.reason" class="w-full rounded-lg border border-gray-300 px-3 py-2" rows="3" placeholder="Nhap ly do dieu chinh cu the" />
            <p v-if="form.errors.reason" class="mt-1 text-sm text-red-500">{{ form.errors.reason }}</p>
          </div>

          <div class="flex justify-end">
            <button
              class="rounded-lg bg-blue-600 px-4 py-2 text-white disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="form.processing || !canSubmit"
              type="submit"
            >
              {{ form.processing ? 'Dang gui...' : 'Gui yeu cau' }}
            </button>
          </div>
        </form>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Lich su yeu cau</h3>
        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Ngay cong</th>
                <th class="p-2">Check-in cu / moi</th>
                <th class="p-2">Check-out cu / moi</th>
                <th class="p-2">Sau dieu chinh</th>
                <th class="p-2">Trang thai</th>
                <th class="p-2">Ly do</th>
                <th class="p-2">Ghi chu duyet</th>
                <th class="p-2">Gui luc</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in adjustments" :key="item.id" class="border-t">
                <td class="p-2">{{ formatDate(item.work_date) }}</td>
                <td class="p-2">{{ formatAdjustmentChange(item.old_check_in_at, item.new_check_in_at) }}</td>
                <td class="p-2">{{ formatAdjustmentChange(item.old_check_out_at, item.new_check_out_at) }}</td>
                <td class="p-2">
                  <div>Vao: {{ formatDateTime(finalAdjustmentCheckIn(item)) }}</div>
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
                <td class="p-4 text-center text-gray-500" colspan="8">Chua co yeu cau dieu chinh nao.</td>
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
const pendingAdjustmentRecordIds = computed(() => new Set(props.adjustments.filter((item) => item.status === 'pending').map((item) => Number(item.attendance_record_id))))
const hasPendingAdjustment = computed(() => selectedRecord.value ? pendingAdjustmentRecordIds.value.has(Number(selectedRecord.value.id)) : false)
const isApprovedRecord = computed(() => selectedRecord.value?.is_confirmed || selectedRecord.value?.approval_status === 'approved')
const checkInMissing = computed(() => Boolean(selectedRecord.value?.missing_check_in) || selectedRecord.value?.day_status === 'missing_check_in' || !selectedRecord.value?.check_in_at)
const checkOutMissing = computed(() => Boolean(selectedRecord.value?.missing_check_out) || selectedRecord.value?.day_status === 'missing_check_out' || !selectedRecord.value?.check_out_at)
const checkInDisabled = computed(() => !selectedRecord.value || isApprovedRecord.value || hasPendingAdjustment.value || (checkOutMissing.value && !checkInMissing.value))
const checkOutDisabled = computed(() => !selectedRecord.value || isApprovedRecord.value || hasPendingAdjustment.value || (checkInMissing.value && !checkOutMissing.value))
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
  if (!resolvedCheckIn.value) return 'Thieu check in'
  if (!resolvedCheckOut.value) return 'Thieu check out'
  if (!isCheckoutAfterCheckin.value) return 'Gio khong hop le'
  if (previewWorkedMinutes.value >= 480) return 'Du cong'
  if (previewWorkedMinutes.value >= 240) return 'Nua cong'
  return 'Khong cong'
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
    messages.push('Chon ban ghi cong can dieu chinh.')
    return messages
  }
  if (isApprovedRecord.value) messages.push('Ban ghi da duoc duyet, nhan vien khong the tu gui dieu chinh.')
  if (hasPendingAdjustment.value) messages.push('Ban ghi nay da co yeu cau dieu chinh dang cho duyet.')
  if (!workDateMatches.value && (form.new_check_in_at || form.new_check_out_at)) messages.push('Thoi gian de xuat phai nam trong dung ngay cong dang chon.')
  if (!isCheckoutAfterCheckin.value) messages.push('Check-out de xuat phai sau check-in de xuat.')
  if (!hasActualChange.value) messages.push('Nhap it nhat mot thoi gian de xuat khac du lieu hien tai.')
  if (checkInDisabled.value && !checkOutDisabled.value) messages.push('Ngay nay dang thieu check-out, chi can bo sung check-out de xuat.')
  if (checkOutDisabled.value && !checkInDisabled.value) messages.push('Ngay nay dang thieu check-in, chi can bo sung check-in de xuat.')
  return messages
})

const canSubmit = computed(() => selectedRecord.value
  && !isApprovedRecord.value
  && !hasPendingAdjustment.value
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
    return `${oldText} -> Giu nguyen`
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
  if (minutes <= 0) return '0 phut'
  const hours = Math.floor(minutes / 60)
  const remainMinutes = minutes % 60
  if (hours <= 0) return `${remainMinutes} phut`
  if (remainMinutes === 0) return `${hours} gio`
  return `${hours} gio ${remainMinutes} phut`
}

function formatWorkResult(item) {
  if (item?.missing_check_in || item?.missing_check_out) return 'Chua tinh cong'
  const unit = Number(item?.work_unit)
  if (unit >= 1) return 'Du cong'
  if (unit >= 0.5) return 'Nua cong'
  return 'Khong cong'
}

function formatDayStatus(value) {
  const labels = {
    present: 'Di lam',
    late: 'Di muon',
    early_leave: 'Ve som',
    leave: 'Nghi phep',
    unpaid_leave: 'Nghi khong phep',
    business_trip: 'Cong tac',
    missing_check_in: 'Thieu check in',
    missing_check_out: 'Thieu check out',
    absent: 'Vang',
  }
  return labels[value] || '-'
}

function formatApprovalStatus(value) {
  const labels = {
    pending: 'Cho duyet',
    approved: 'Da duyet',
    rejected: 'Tu choi',
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
    form.setError('attendance_record_id', 'Chon ban ghi cong can dieu chinh.')
    return
  }

  if (isApprovedRecord.value) {
    form.setError('attendance_record_id', 'Ban ghi da duoc duyet, vui long lien he HR de dieu chinh.')
  }

  if (hasPendingAdjustment.value) {
    form.setError('attendance_record_id', 'Ban ghi nay da co yeu cau dieu chinh dang cho duyet.')
  }

  if (!workDateMatches.value) {
    form.setError('new_check_in_at', 'Thoi gian de xuat phai nam trong dung ngay cong dang chon.')
    form.setError('new_check_out_at', 'Thoi gian de xuat phai nam trong dung ngay cong dang chon.')
  }

  if (!isCheckoutAfterCheckin.value) {
    form.setError('new_check_out_at', 'Check-out de xuat phai sau check-in sau dieu chinh.')
  }

  if (!hasActualChange.value) {
    form.setError('new_check_in_at', 'Nhap thoi gian de xuat khac du lieu hien tai.')
    form.setError('new_check_out_at', 'Nhap thoi gian de xuat khac du lieu hien tai.')
  }

  if (!String(form.reason || '').trim()) {
    form.setError('reason', 'Ly do dieu chinh la bat buoc.')
  }
}
</script>
