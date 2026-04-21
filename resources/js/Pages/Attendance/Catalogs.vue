<template>
  <Head title="Danh muc cham cong" />

  <AdminLayout>
    <PageBreadcrumb title="Danh muc cham cong" :items="[{ text: 'Cham cong', link: null }, { text: 'Danh muc', link: null }]" />

    <div class="space-y-6">
      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-1">
          <h3 class="text-lg font-semibold text-gray-900">Ca lam viec</h3>
          <p class="text-sm text-gray-500">Khai bao gio vao, gio ra, nghi giua ca, phut chuan, nguong nua cong va quy tac tinh muon som/tang ca.</p>
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="submitShift">
          <div v-if="editingShiftId" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Dang chinh sua ca lam <strong>{{ shiftForm.shift_name || `#${editingShiftId}` }}</strong>.
          </div>

          <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
            <div class="mb-4">
              <h4 class="text-sm font-semibold text-gray-900">Cau hinh ca lam viec</h4>
              <p class="mt-1 text-sm text-gray-500">Phan nay dung de khai bao gio hanh chinh, nghi giua ca va quy tac tinh cong.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
              <Field label="Ten ca" :error="shiftForm.errors.shift_name">
                <input v-model.trim="shiftForm.shift_name" class="form-input" placeholder="Ca hanh chinh">
              </Field>
              <InputDate v-model="shiftForm.start_time" label="Gio bat dau" placeholder="Chon gio" :config="timePickerConfig" :error="shiftForm.errors.start_time" />
              <InputDate v-model="shiftForm.end_time" label="Gio ket thuc" placeholder="Chon gio" :config="timePickerConfig" :error="shiftForm.errors.end_time" />
              <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600">
                Ma ca duoc tao tu dong khi luu.
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
              <InputDate v-model="shiftForm.break_start_time" label="Bat dau nghi giua ca" placeholder="Khong nghi" :config="timePickerConfig" :error="shiftForm.errors.break_start_time" />
              <InputDate v-model="shiftForm.break_end_time" label="Ket thuc nghi giua ca" placeholder="Khong nghi" :config="timePickerConfig" :error="shiftForm.errors.break_end_time" />
              <Field label="Phut chuan" :error="shiftForm.errors.standard_minutes">
                <input v-model.number="shiftForm.standard_minutes" class="form-input bg-gray-100 text-gray-700" type="number" min="1" readonly>
                <p class="text-xs text-gray-500">Tu tinh bang thoi luong ca tru nghi giua ca va nghi giao ca.</p>
              </Field>
              <Field label="Nguong nua cong" :error="shiftForm.errors.half_day_minutes">
                <input v-model.number="shiftForm.half_day_minutes" class="form-input bg-gray-100 text-gray-700" type="number" min="1" readonly>
                <p class="text-xs text-gray-500">Tu tinh bang 50% phut chuan.</p>
              </Field>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
              <Field label="Dung sai chung" :error="shiftForm.errors.grace_minutes">
                <input v-model.number="shiftForm.grace_minutes" class="form-input" type="number" min="0" max="180">
              </Field>
              <Field label="Nghi giao ca (phut)" :error="shiftForm.errors.handover_break_minutes">
                <input v-model.number="shiftForm.handover_break_minutes" class="form-input" type="number" min="0" max="240">
              </Field>
              <div class="md:col-span-2 rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600">
                So phut nay dung de ghi nhan quy tac giao ca, khong bi tru khoi phut chuan va thoi gian tinh cong.
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
              <div class="grid grid-cols-2 gap-3 md:col-start-4">
                <ToggleBox v-model="shiftForm.allows_overtime" label="Tinh tang ca" />
                <ToggleBox v-model="shiftForm.is_overnight" label="Ca qua dem" />
              </div>
            </div>
          </div>

          <div class="rounded-xl border border-blue-200 bg-blue-50/60 p-4">
            <div class="mb-4">
              <h4 class="text-sm font-semibold text-blue-900">Quy tac tang ca</h4>
              <p class="mt-1 text-sm text-blue-800">Phan nay la cau hinh rieng cho tang ca cua ca lam. Co the bo trong neu ca khong su dung OT co quy tac rieng.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
              <InputDate v-model="shiftForm.overtime_start_time" label="Gio bat dau tang ca" placeholder="Khong cau hinh" :config="timePickerConfig" :error="shiftForm.errors.overtime_start_time" />
              <InputDate v-model="shiftForm.overtime_end_time" label="Gio ket thuc tang ca" placeholder="Khong cau hinh" :config="timePickerConfig" :error="shiftForm.errors.overtime_end_time" />
              <Field label="Tien tang ca / gio" :error="shiftForm.errors.overtime_hourly_rate">
                <input v-model.number="shiftForm.overtime_hourly_rate" class="form-input" type="number" min="0" step="1000" placeholder="Vi du: 50000">
              </Field>
            </div>
          </div>

          <Field label="Ghi chu" :error="shiftForm.errors.description">
            <textarea v-model.trim="shiftForm.description" class="form-input min-h-[74px]" placeholder="Quy dinh rieng cua ca lam neu co"></textarea>
          </Field>

          <div class="rounded-lg border p-4 text-sm" :class="shiftPreviewError ? 'border-red-200 bg-red-50 text-red-700' : 'border-blue-100 bg-blue-50 text-blue-900'">
            <div class="font-semibold">Tom tat ca sau khi luu</div>
            <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-4">
              <div>Tong thoi gian tu dau ca den cuoi ca: <strong>{{ formatMinutes(shiftDurationMinutes) }}</strong></div>
              <div>Thoi gian nghi giua ca: <strong>{{ formatMinutes(breakMinutes) }}</strong></div>
              <div>Thoi luong ca sau khi tru nghi: <strong>{{ formatMinutes(netShiftMinutes) }}</strong></div>
              <div>Thoi gian tinh cong theo phut chuan: <strong>{{ formatMinutes(standardMinutes) }}</strong></div>
              <div>Nghi giao ca: <strong>{{ formatMinutes(Number(shiftForm.handover_break_minutes || 0)) }}</strong></div>
              <div>Khung tang ca: <strong>{{ overtimePreviewLabel }}</strong></div>
              <div>Trang thai kiem tra du lieu: <strong>{{ shiftPreviewError || 'Hop le de luu' }}</strong></div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              v-if="editingShiftId"
              class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700"
              type="button"
              @click="cancelShiftEdit"
            >
              Huy sua
            </button>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="shiftForm.processing || !!shiftPreviewError">
              {{ shiftForm.processing ? 'Dang luu...' : (editingShiftId ? 'Luu ca lam' : 'Them ca') }}
            </button>
          </div>
        </form>

        <TableShell class="mt-5">
          <thead>
            <tr class="text-left">
              <th class="p-2">Ma / Ten ca</th>
              <th class="p-2">Khung gio</th>
              <th class="p-2">Nghi giua ca</th>
              <th class="p-2">Nguong cong</th>
              <th class="p-2">Grace</th>
              <th class="p-2">Trang thai</th>
              <th class="p-2">Tac vu</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in workShifts" :key="item.id" class="border-t">
              <td class="p-2">
                <div class="font-semibold text-gray-900">{{ item.shift_name }}</div>
              </td>
              <td class="p-2">{{ item.start_time }} - {{ item.end_time }}<span v-if="item.is_overnight"> (+1)</span></td>
              <td class="p-2">{{ item.break_start_time && item.break_end_time ? `${item.break_start_time} - ${item.break_end_time}` : 'Khong co' }}</td>
              <td class="p-2">
                <div>{{ item.standard_minutes }}p / nua cong {{ item.half_day_minutes }}p</div>
                <div class="text-xs text-gray-500">Nghi giao ca {{ item.handover_break_minutes || 0 }}p</div>
              </td>
              <td class="p-2">
                <div>Dung sai chung {{ item.grace_minutes }}p</div>
                <div class="text-xs text-gray-500">
                  OT: {{ item.overtime_start_time && item.overtime_end_time ? `${item.overtime_start_time} - ${item.overtime_end_time}` : 'Khong cau hinh' }}
                  <span v-if="item.overtime_hourly_rate"> | {{ formatMoney(item.overtime_hourly_rate) }}/gio</span>
                </div>
              </td>
              <td class="p-2">
                <StatusBadge :active="item.is_active" />
              </td>
              <td class="p-2 space-x-2">
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="editShift(item)">Chinh sua</button>
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="startQuickAssign(item)">Gan nhan vien</button>
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="toggleShift(item.id)">
                  {{ item.is_active ? 'Ngung dung' : 'Kich hoat' }}
                </button>
              </td>
            </tr>
          </tbody>
        </TableShell>

        <div v-if="quickAssignShift" class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-base font-semibold text-blue-900">Gan nhan vien vao ca lam</h4>
              <p class="mt-1 text-sm text-blue-800">
                Dang chon ca <strong>{{ quickAssignShift.shift_name }}</strong>. Ban co the tao phan ca cho nhan vien ngay tai day.
              </p>
            </div>
            <button class="rounded-lg border border-blue-300 bg-white px-3 py-1.5 text-sm font-medium text-blue-800" type="button" @click="cancelQuickAssign">
              Dong
            </button>
          </div>

          <form class="mt-4 space-y-4" @submit.prevent="submitQuickAssignment">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
              <Field label="Nhan vien" :error="assignmentForm.errors.employee_profile_id">
                <select v-model="assignmentForm.employee_profile_id" class="form-input">
                  <option :value="null">Chon nhan vien</option>
                  <option v-for="item in employeeOptions" :key="item.id" :value="item.id">{{ item.label }}</option>
                </select>
              </Field>
              <Field label="Ca lam">
                <input class="form-input bg-gray-50" :value="quickAssignShift.shift_name" disabled>
              </Field>
              <InputDate v-model="assignmentForm.effective_from" label="Tu ngay" placeholder="Chon ngay" :config="datePickerConfig" :error="assignmentForm.errors.effective_from" />
              <InputDate v-model="assignmentForm.effective_to" label="Den ngay" placeholder="Bo trong neu chua ket thuc" :config="datePickerConfig" :error="assignmentForm.errors.effective_to" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_2fr]">
              <Field label="Ngay trong tuan" :error="assignmentForm.errors.weekdays">
                <div class="flex flex-wrap gap-2">
                  <label v-for="day in weekdays" :key="`quick-${day.value}`" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm">
                    <input v-model="assignmentForm.weekdays" type="checkbox" :value="day.value">
                    {{ day.label }}
                  </label>
                </div>
              </Field>
              <Field label="Ghi chu" :error="assignmentForm.errors.note">
                <input v-model.trim="assignmentForm.note" class="form-input" placeholder="Ghi chu phan ca neu can">
              </Field>
            </div>

            <div class="flex justify-end gap-3">
              <button class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700" type="button" @click="cancelQuickAssign">
                Huy
              </button>
              <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="assignmentForm.processing">
                {{ assignmentForm.processing ? 'Dang luu...' : 'Gan vao ca' }}
              </button>
            </div>
          </form>
        </div>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-1">
          <h3 class="text-lg font-semibold text-gray-900">Ngay le</h3>
          <p class="text-sm text-gray-500">Khai bao ngay nghi ap dung toan cong ty, phan loai ngay nghi va xac dinh co tinh luong hay lap lai hang nam.</p>
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="submitHoliday">
          <div v-if="editingHolidayId" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Dang chinh sua ngay le <strong>{{ holidayForm.holiday_name || `#${editingHolidayId}` }}</strong>.
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <InputDate v-model="holidayForm.holiday_date" label="Ngay le" placeholder="Chon ngay" :config="datePickerConfig" :error="holidayForm.errors.holiday_date" />
            <Field label="Ten ngay le" :error="holidayForm.errors.holiday_name">
              <input v-model.trim="holidayForm.holiday_name" class="form-input" placeholder="Ten ngay le">
            </Field>
            <Field label="Loai ngay" :error="holidayForm.errors.holiday_type">
              <select v-model="holidayForm.holiday_type" class="form-input">
                <option value="public">Le nha nuoc</option>
                <option value="company">Ngay nghi cong ty</option>
                <option value="compensatory">Nghi bu</option>
                <option value="special">Dac biet</option>
              </select>
            </Field>
            <div class="grid grid-cols-2 gap-3">
              <ToggleBox v-model="holidayForm.is_paid_leave" label="Co luong" />
              <ToggleBox v-model="holidayForm.is_recurring" label="Lap lai hang nam" />
            </div>
          </div>

          <Field label="Ghi chu" :error="holidayForm.errors.note">
            <input v-model.trim="holidayForm.note" class="form-input" placeholder="Ghi chu neu co">
          </Field>

          <div class="flex justify-end gap-3">
            <button
              v-if="editingHolidayId"
              class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700"
              type="button"
              @click="cancelHolidayEdit"
            >
              Huy sua
            </button>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="holidayForm.processing">
              {{ holidayForm.processing ? 'Dang luu...' : (editingHolidayId ? 'Luu ngay le' : 'Them ngay le') }}
            </button>
          </div>
        </form>

        <TableShell class="mt-5">
          <thead>
            <tr class="text-left">
              <th class="p-2">Ngay</th>
              <th class="p-2">Ten</th>
              <th class="p-2">Loai</th>
              <th class="p-2">Tinh luong</th>
              <th class="p-2">Lap lai</th>
              <th class="p-2">Tac vu</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in holidays" :key="item.id" class="border-t">
              <td class="p-2">{{ formatDate(item.holiday_date) }}</td>
              <td class="p-2">{{ item.holiday_name }}</td>
              <td class="p-2">{{ holidayTypeLabel(item.holiday_type) }}</td>
              <td class="p-2">{{ item.is_paid_leave ? 'Co luong' : 'Khong luong' }}</td>
              <td class="p-2">{{ item.is_recurring ? 'Hang nam' : '-' }}</td>
              <td class="p-2 space-x-2">
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="editHoliday(item)">Chinh sua</button>
                <button class="rounded border px-3 py-1 text-sm text-red-600" type="button" @click="deleteHoliday(item.id)">Xoa</button>
              </td>
            </tr>
          </tbody>
        </TableShell>
      </section>

      <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <div class="flex flex-col gap-1">
          <h3 class="text-lg font-semibold text-gray-900">Phan ca</h3>
          <p class="text-sm text-gray-500">Gan ca cho nhan vien hoac phong ban trong mot khoang thoi gian cu the, co the gioi han theo ngay trong tuan.</p>
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="submitAssignment">
          <div v-if="editingAssignmentId" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Dang chinh sua phan ca <strong>#{{ editingAssignmentId }}</strong>.
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
            <Field label="Ap dung cho" :error="assignmentForm.errors.target_type">
              <select v-model="assignmentForm.target_type" class="form-input">
                <option value="employee">Nhan vien</option>
                <option value="department">Phong ban</option>
              </select>
            </Field>
            <Field v-if="assignmentForm.target_type === 'employee'" label="Nhan vien" :error="assignmentForm.errors.employee_profile_id">
              <select v-model="assignmentForm.employee_profile_id" class="form-input">
                <option :value="null">Chon nhan vien</option>
                <option v-for="item in employeeOptions" :key="item.id" :value="item.id">{{ item.label }}</option>
              </select>
            </Field>
            <Field v-else label="Phong ban" :error="assignmentForm.errors.department_id">
              <select v-model="assignmentForm.department_id" class="form-input">
                <option :value="null">Chon phong ban</option>
                <option v-for="item in departmentOptions" :key="item.id" :value="item.id">{{ item.name }}</option>
              </select>
            </Field>
            <Field label="Ca lam" :error="assignmentForm.errors.work_shift_id">
              <select v-model="assignmentForm.work_shift_id" class="form-input">
                <option :value="null">Chon ca lam</option>
                <option v-for="item in activeWorkShifts" :key="item.id" :value="item.id">{{ item.shift_name }}</option>
              </select>
            </Field>
            <InputDate v-model="assignmentForm.effective_from" label="Tu ngay" placeholder="Chon ngay" :config="datePickerConfig" :error="assignmentForm.errors.effective_from" />
            <InputDate v-model="assignmentForm.effective_to" label="Den ngay" placeholder="Bo trong neu chua ket thuc" :config="datePickerConfig" :error="assignmentForm.errors.effective_to" />
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_2fr]">
            <Field label="Ngay trong tuan" :error="assignmentForm.errors.weekdays">
              <div class="flex flex-wrap gap-2">
                <label v-for="day in weekdays" :key="day.value" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm">
                  <input v-model="assignmentForm.weekdays" type="checkbox" :value="day.value">
                  {{ day.label }}
                </label>
              </div>
            </Field>
            <Field label="Ghi chu" :error="assignmentForm.errors.note">
              <input v-model.trim="assignmentForm.note" class="form-input" placeholder="Ly do/ghi chu phan ca">
            </Field>
          </div>

          <div v-if="assignmentForm.errors.employee_profile_id || assignmentForm.errors.department_id" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ assignmentForm.errors.employee_profile_id || assignmentForm.errors.department_id }}
          </div>

          <div class="flex justify-end gap-3">
            <button
              v-if="editingAssignmentId"
              class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700"
              type="button"
              @click="cancelAssignmentEdit"
            >
              Huy sua
            </button>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60" :disabled="assignmentForm.processing">
              {{ assignmentForm.processing ? 'Dang luu...' : (editingAssignmentId ? 'Luu phan ca' : 'Them phan ca') }}
            </button>
          </div>
        </form>

        <TableShell class="mt-5">
          <thead>
            <tr class="text-left">
              <th class="p-2">Doi tuong</th>
              <th class="p-2">Ca</th>
              <th class="p-2">Hieu luc</th>
              <th class="p-2">Ngay ap dung</th>
              <th class="p-2">Trang thai</th>
              <th class="p-2">Tac vu</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in assignments" :key="item.id" class="border-t">
              <td class="p-2">
                <div class="font-semibold text-gray-900">{{ item.employee_name || item.department_name || '-' }}</div>
                <div class="text-xs text-gray-500">{{ item.target_type === 'employee' ? 'Nhan vien' : 'Phong ban' }}</div>
              </td>
              <td class="p-2">{{ item.work_shift_name }}</td>
              <td class="p-2">{{ formatDate(item.effective_from) }} - {{ item.effective_to ? formatDate(item.effective_to) : 'Khong gioi han' }}</td>
              <td class="p-2">{{ weekdayLabels(item.weekdays) }}</td>
              <td class="p-2"><StatusBadge :active="item.is_active" /></td>
              <td class="p-2 space-x-2">
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="editAssignment(item)">Chinh sua</button>
                <button class="rounded border px-3 py-1 text-sm" type="button" @click="toggleAssignment(item.id)">
                  {{ item.is_active ? 'Ngung dung' : 'Kich hoat' }}
                </button>
              </td>
            </tr>
          </tbody>
        </TableShell>
      </section>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, h, ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import InputDate from '@/components/forms/InputDate.vue'

const props = defineProps({
  workShifts: { type: Array, default: () => [] },
  holidays: { type: Array, default: () => [] },
  assignments: { type: Array, default: () => [] },
  employeeOptions: { type: Array, default: () => [] },
  departmentOptions: { type: Array, default: () => [] },
})

const Field = (props, { slots }) => h('div', { class: 'space-y-2' }, [
  h('label', { class: 'block text-sm font-medium text-gray-700' }, props.label),
  slots.default?.(),
  props.error ? h('p', { class: 'text-sm text-red-500' }, Array.isArray(props.error) ? props.error[0] : props.error) : null,
])
Field.props = ['label', 'error']

const ToggleBox = (props, { emit }) => h('label', { class: 'flex h-full items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700' }, [
  h('input', {
    checked: props.modelValue,
    type: 'checkbox',
    onChange: (event) => emit('update:modelValue', event.target.checked),
  }),
  props.label,
])
ToggleBox.props = ['modelValue', 'label']
ToggleBox.emits = ['update:modelValue']

const StatusBadge = (props) => h('span', {
  class: [
    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
    props.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600',
  ],
}, props.active ? 'Dang dung' : 'Ngung dung')
StatusBadge.props = ['active']

const TableShell = (props, { slots }) => h('div', { class: ['overflow-auto', props.class] }, [
  h('table', { class: 'min-w-full text-sm' }, slots.default?.()),
])
TableShell.props = ['class']

const datePickerConfig = {
  dateFormat: 'Y-m-d',
  altFormat: 'd/m/Y',
  allowInput: false,
}

const timePickerConfig = {
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  altFormat: 'H:i',
  time_24hr: true,
  allowInput: false,
}

const weekdays = [
  { value: 1, label: 'T2' },
  { value: 2, label: 'T3' },
  { value: 3, label: 'T4' },
  { value: 4, label: 'T5' },
  { value: 5, label: 'T6' },
  { value: 6, label: 'T7' },
  { value: 7, label: 'CN' },
]

const editingShiftId = ref(null)
const editingHolidayId = ref(null)
const editingAssignmentId = ref(null)
const quickAssignShift = ref(null)

const shiftForm = useForm({
  shift_name: '',
  start_time: '08:00',
  end_time: '17:00',
  break_start_time: '12:00',
  break_end_time: '13:00',
  overtime_start_time: '',
  overtime_end_time: '',
  standard_minutes: 480,
  half_day_minutes: 240,
  handover_break_minutes: 0,
  overtime_hourly_rate: '',
  grace_minutes: 0,
  late_grace_minutes: 0,
  early_leave_grace_minutes: 0,
  allows_overtime: true,
  is_overnight: false,
  is_active: true,
  description: '',
})

const holidayForm = useForm({
  holiday_date: '',
  holiday_name: '',
  holiday_type: 'public',
  is_paid_leave: true,
  is_recurring: false,
  note: '',
})

const assignmentForm = useForm({
  target_type: 'employee',
  employee_profile_id: null,
  department_id: null,
  work_shift_id: null,
  effective_from: '',
  effective_to: '',
  weekdays: [1, 2, 3, 4, 5],
  note: '',
  is_active: true,
})

const activeWorkShifts = computed(() => props.workShifts.filter((shift) => shift.is_active))

const shiftDurationMinutes = computed(() => timeRangeMinutes(shiftForm.start_time, shiftForm.end_time, shiftForm.is_overnight))
const breakMinutes = computed(() => {
  if (!shiftForm.break_start_time || !shiftForm.break_end_time) return 0
  return timeRangeMinutes(shiftForm.break_start_time, shiftForm.break_end_time, shiftForm.is_overnight)
})
const netShiftMinutes = computed(() => Math.max(0, shiftDurationMinutes.value - breakMinutes.value))
const standardMinutes = computed(() => Number(shiftForm.standard_minutes || 0))
const overtimeGapMinutes = computed(() => {
  if (!shiftForm.overtime_start_time || !shiftForm.end_time) return 0
  const shiftStartMinutes = timeToMinutes(shiftForm.start_time)
  let shiftEndMinutes = timeToMinutes(shiftForm.end_time)
  let overtimeStartMinutes = timeToMinutes(shiftForm.overtime_start_time)

  if (shiftForm.is_overnight) {
    if (shiftEndMinutes <= shiftStartMinutes) shiftEndMinutes += 1440
    if (overtimeStartMinutes < shiftStartMinutes) overtimeStartMinutes += 1440
  }

  return overtimeStartMinutes - shiftEndMinutes
})
const overtimePreviewLabel = computed(() => {
  if (!shiftForm.overtime_start_time || !shiftForm.overtime_end_time) return 'Chua cau hinh'
  return `${shiftForm.overtime_start_time} - ${shiftForm.overtime_end_time}`
})
const shiftPreviewError = computed(() => {
  if (shiftDurationMinutes.value <= 0) return 'Gio ket thuc phai sau gio bat dau'
  if ((shiftForm.break_start_time && !shiftForm.break_end_time) || (!shiftForm.break_start_time && shiftForm.break_end_time)) return 'Can nhap du gio nghi'
  if ((shiftForm.overtime_start_time && !shiftForm.overtime_end_time) || (!shiftForm.overtime_start_time && shiftForm.overtime_end_time)) return 'Can nhap du gio tang ca'
  if (breakMinutes.value < 0 || breakMinutes.value >= shiftDurationMinutes.value || !rangeInsideShift(shiftForm.start_time, shiftForm.end_time, shiftForm.break_start_time, shiftForm.break_end_time, shiftForm.is_overnight)) return 'Gio nghi khong hop le'
  if (Number(shiftForm.handover_break_minutes || 0) < 0) return 'Nghi giao ca khong hop le'
  if (!shiftForm.allows_overtime && (shiftForm.overtime_start_time || shiftForm.overtime_end_time || shiftForm.overtime_hourly_rate)) return 'Dang tat tinh tang ca nhung van con cau hinh OT'
  if (standardMinutes.value !== netShiftMinutes.value) return `Phut chuan phai bang ${netShiftMinutes.value} phut`
  if (Number(shiftForm.half_day_minutes) > Number(shiftForm.standard_minutes)) return 'Nguong nua cong vuot phut chuan'
  if (shiftForm.overtime_start_time && shiftForm.overtime_end_time && overtimeGapMinutes.value > 0) return `Khong duoc de khoang ho ${shiftForm.end_time}-${shiftForm.overtime_start_time}`
  if (shiftForm.overtime_start_time && shiftForm.overtime_end_time && overtimeGapMinutes.value < 0) return 'Gio bat dau tang ca phai noi tiep ngay sau gio ket thuc ca'
  return ''
})

watch(() => assignmentForm.target_type, (type) => {
  if (type === 'employee') {
    assignmentForm.department_id = null
  } else {
    assignmentForm.employee_profile_id = null
  }
})

watch(() => shiftForm.grace_minutes, (value) => {
  const normalized = Number(value || 0)
  shiftForm.late_grace_minutes = normalized
  shiftForm.early_leave_grace_minutes = normalized
})

watch(
  () => [
    shiftForm.start_time,
    shiftForm.end_time,
    shiftForm.break_start_time,
    shiftForm.break_end_time,
    shiftForm.is_overnight,
  ],
  () => syncShiftWorkThresholds(),
  { immediate: true }
)

function syncShiftWorkThresholds() {
  if (!canAutoCalculateShiftThresholds()) return

  const minutes = netShiftMinutes.value
  shiftForm.standard_minutes = minutes
  shiftForm.half_day_minutes = Math.ceil(minutes / 2)
}

function canAutoCalculateShiftThresholds() {
  const hasBreakPair = Boolean(shiftForm.break_start_time) === Boolean(shiftForm.break_end_time)
  return shiftDurationMinutes.value > 0
    && hasBreakPair
    && breakMinutes.value >= 0
    && rangeInsideShift(shiftForm.start_time, shiftForm.end_time, shiftForm.break_start_time, shiftForm.break_end_time, shiftForm.is_overnight)
    && netShiftMinutes.value > 0
}

function resetShiftForm() {
  shiftForm.reset()
  shiftForm.start_time = '08:00'
  shiftForm.end_time = '17:00'
  shiftForm.break_start_time = '12:00'
  shiftForm.break_end_time = '13:00'
  shiftForm.overtime_start_time = ''
  shiftForm.overtime_end_time = ''
  shiftForm.standard_minutes = 480
  shiftForm.half_day_minutes = 240
  shiftForm.handover_break_minutes = 0
  shiftForm.overtime_hourly_rate = ''
  shiftForm.grace_minutes = 0
  shiftForm.late_grace_minutes = 0
  shiftForm.early_leave_grace_minutes = 0
  shiftForm.allows_overtime = true
  shiftForm.is_overnight = false
  shiftForm.is_active = true
  shiftForm.clearErrors()
  editingShiftId.value = null
}

function resetHolidayForm() {
  holidayForm.reset()
  holidayForm.holiday_type = 'public'
  holidayForm.is_paid_leave = true
  holidayForm.is_recurring = false
  holidayForm.clearErrors()
  editingHolidayId.value = null
}

function resetAssignmentForm() {
  assignmentForm.reset()
  assignmentForm.target_type = 'employee'
  assignmentForm.employee_profile_id = null
  assignmentForm.department_id = null
  assignmentForm.work_shift_id = null
  assignmentForm.weekdays = [1, 2, 3, 4, 5]
  assignmentForm.is_active = true
  assignmentForm.clearErrors()
  editingAssignmentId.value = null
}

const submitShift = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => resetShiftForm(),
  }
  return editingShiftId.value
    ? shiftForm.put(route('attendance.catalogs.work-shifts.update', editingShiftId.value), options)
    : shiftForm.post(route('attendance.catalogs.work-shifts.store'), options)
}

const submitHoliday = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => resetHolidayForm(),
  }
  return editingHolidayId.value
    ? holidayForm.put(route('attendance.catalogs.holidays.update', editingHolidayId.value), options)
    : holidayForm.post(route('attendance.catalogs.holidays.store'), options)
}

const submitAssignment = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => resetAssignmentForm(),
  }
  return editingAssignmentId.value
    ? assignmentForm.put(route('attendance.catalogs.assignments.update', editingAssignmentId.value), options)
    : assignmentForm.post(route('attendance.catalogs.assignments.store'), options)
}

const submitQuickAssignment = () => {
  assignmentForm.target_type = 'employee'
  assignmentForm.department_id = null
  const options = {
    preserveScroll: true,
    onSuccess: () => {
      resetAssignmentForm()
      quickAssignShift.value = null
    },
  }
  return assignmentForm.post(route('attendance.catalogs.assignments.store'), options)
}

function editShift(item) {
  editingShiftId.value = item.id
  shiftForm.shift_name = item.shift_name || ''
  shiftForm.start_time = item.start_time || '08:00'
  shiftForm.end_time = item.end_time || '17:00'
  shiftForm.break_start_time = item.break_start_time || ''
  shiftForm.break_end_time = item.break_end_time || ''
  shiftForm.overtime_start_time = item.overtime_start_time || ''
  shiftForm.overtime_end_time = item.overtime_end_time || ''
  shiftForm.standard_minutes = Number(item.standard_minutes || 480)
  shiftForm.half_day_minutes = Number(item.half_day_minutes || 240)
  shiftForm.handover_break_minutes = Number(item.handover_break_minutes || 0)
  shiftForm.overtime_hourly_rate = item.overtime_hourly_rate ?? ''
  const unifiedGrace = Number(item.grace_minutes || item.late_grace_minutes || item.early_leave_grace_minutes || 0)
  shiftForm.grace_minutes = unifiedGrace
  shiftForm.late_grace_minutes = unifiedGrace
  shiftForm.early_leave_grace_minutes = unifiedGrace
  shiftForm.allows_overtime = !!item.allows_overtime
  shiftForm.is_overnight = !!item.is_overnight
  shiftForm.is_active = !!item.is_active
  shiftForm.description = item.description || ''
  shiftForm.clearErrors()
}

function editHoliday(item) {
  editingHolidayId.value = item.id
  holidayForm.holiday_date = item.holiday_date || ''
  holidayForm.holiday_name = item.holiday_name || ''
  holidayForm.holiday_type = item.holiday_type || 'public'
  holidayForm.is_paid_leave = !!item.is_paid_leave
  holidayForm.is_recurring = !!item.is_recurring
  holidayForm.note = item.note || ''
  holidayForm.clearErrors()
}

function editAssignment(item) {
  editingAssignmentId.value = item.id
  assignmentForm.target_type = item.target_type || 'employee'
  assignmentForm.employee_profile_id = item.employee_profile_id ?? null
  assignmentForm.department_id = item.department_id ?? null
  assignmentForm.work_shift_id = item.work_shift_id ?? null
  assignmentForm.effective_from = item.effective_from || ''
  assignmentForm.effective_to = item.effective_to || ''
  assignmentForm.weekdays = Array.isArray(item.weekdays) ? item.weekdays.map(Number) : [1, 2, 3, 4, 5]
  assignmentForm.note = item.note || ''
  assignmentForm.is_active = !!item.is_active
  assignmentForm.clearErrors()
}

const cancelShiftEdit = () => resetShiftForm()
const cancelHolidayEdit = () => resetHolidayForm()
const cancelAssignmentEdit = () => resetAssignmentForm()

function startQuickAssign(item) {
  quickAssignShift.value = item
  assignmentForm.clearErrors()
  assignmentForm.target_type = 'employee'
  assignmentForm.employee_profile_id = null
  assignmentForm.department_id = null
  assignmentForm.work_shift_id = item.id
  assignmentForm.effective_from = ''
  assignmentForm.effective_to = ''
  assignmentForm.weekdays = [1, 2, 3, 4, 5]
  assignmentForm.note = ''
  assignmentForm.is_active = true
}

function cancelQuickAssign() {
  quickAssignShift.value = null
  resetAssignmentForm()
}

const toggleShift = (id) => router.put(route('attendance.catalogs.work-shifts.toggle', id), {}, {
  preserveScroll: true,
  onError: (errors) => {
    toast.error(errors.work_shift || 'Khong the doi trang thai ca lam.')
  },
})
const deleteHoliday = (id) => {
  if (confirm('Xoa ngay le nay?')) {
    router.delete(route('attendance.catalogs.holidays.destroy', id), { preserveScroll: true })
  }
}
const toggleAssignment = (id) => router.put(route('attendance.catalogs.assignments.toggle', id), {}, { preserveScroll: true })

function timeRangeMinutes(start, end, overnight = false) {
  if (!start || !end) return 0
  const startMinutes = timeToMinutes(start)
  let endMinutes = timeToMinutes(end)
  if (overnight && endMinutes <= startMinutes) endMinutes += 1440
  return endMinutes - startMinutes
}

function timeToMinutes(value) {
  const [hour, minute] = String(value).slice(0, 5).split(':').map(Number)
  if (Number.isNaN(hour) || Number.isNaN(minute)) return 0
  return hour * 60 + minute
}

function rangeInsideShift(shiftStart, shiftEnd, rangeStart, rangeEnd, overnight = false) {
  if (!rangeStart && !rangeEnd) return true
  if (!rangeStart || !rangeEnd) return false
  let shiftStartMinutes = timeToMinutes(shiftStart)
  let shiftEndMinutes = timeToMinutes(shiftEnd)
  let rangeStartMinutes = timeToMinutes(rangeStart)
  let rangeEndMinutes = timeToMinutes(rangeEnd)
  if (overnight) {
    if (shiftEndMinutes <= shiftStartMinutes) shiftEndMinutes += 1440
    if (rangeStartMinutes < shiftStartMinutes) rangeStartMinutes += 1440
    if (rangeEndMinutes <= rangeStartMinutes) rangeEndMinutes += 1440
  }
  return rangeStartMinutes >= shiftStartMinutes && rangeEndMinutes <= shiftEndMinutes && rangeEndMinutes > rangeStartMinutes
}

function formatMinutes(minutes) {
  if (!minutes || minutes <= 0) return '0 phut'
  const hours = Math.floor(minutes / 60)
  const rest = minutes % 60
  if (!hours) return `${rest} phut`
  return rest ? `${hours} gio ${rest} phut` : `${hours} gio`
}

function formatMoney(value) {
  const amount = Number(value || 0)
  return new Intl.NumberFormat('vi-VN').format(amount)
}

function formatDate(value) {
  if (!value) return '-'
  const [year, month, day] = String(value).slice(0, 10).split('-')
  return `${day}/${month}/${year}`
}

function holidayTypeLabel(type) {
  return {
    public: 'Le nha nuoc',
    company: 'Ngay nghi cong ty',
    compensatory: 'Nghi bu',
    special: 'Dac biet',
  }[type] || type || '-'
}

function weekdayLabels(days) {
  if (!days || !days.length) return 'Tat ca ngay'
  const labels = new Map(weekdays.map((day) => [day.value, day.label]))
  return days.map((day) => labels.get(Number(day))).filter(Boolean).join(', ')
}
</script>

<style scoped>
.form-input {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 0.625rem 0.75rem;
  color: #111827;
  outline: none;
}

.form-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgb(37 99 235 / 0.15);
}
</style>
