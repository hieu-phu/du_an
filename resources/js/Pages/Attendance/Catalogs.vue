<template>
  <Head title="Danh muc cham cong" />

  <AdminLayout>
    <PageBreadcrumb title="Danh muc cham cong" :items="[{ text: 'Cham cong', link: null }, { text: 'Danh muc', link: null }]" />

    <div class="space-y-6">
      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Ca lam viec</h3>
        <form class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4" @submit.prevent="submitShift">
          <input v-model="shiftForm.shift_name" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Ten ca">
          <input v-model="shiftForm.start_time" class="rounded-lg border border-gray-300 px-3 py-2" type="time">
          <input v-model="shiftForm.end_time" class="rounded-lg border border-gray-300 px-3 py-2" type="time">
          <input v-model.number="shiftForm.standard_minutes" class="rounded-lg border border-gray-300 px-3 py-2" type="number" min="1" placeholder="Phut chuan">
          <button class="rounded-lg bg-blue-600 px-4 py-2 text-white">Them ca</button>
        </form>

        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Ten ca</th>
                <th class="p-2">Khung gio</th>
                <th class="p-2">Phut chuan</th>
                <th class="p-2">Trang thai</th>
                <th class="p-2">Tac vu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in workShifts" :key="item.id" class="border-t">
                <td class="p-2">{{ item.shift_name }}</td>
                <td class="p-2">{{ item.start_time }} - {{ item.end_time }}</td>
                <td class="p-2">{{ item.standard_minutes }}</td>
                <td class="p-2">{{ item.is_active ? 'Active' : 'Inactive' }}</td>
                <td class="p-2">
                  <button class="rounded border px-2 py-1" type="button" @click="toggleShift(item.id)">Toggle</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Ngay le</h3>
        <form class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4" @submit.prevent="submitHoliday">
          <input v-model="holidayForm.holiday_date" class="rounded-lg border border-gray-300 px-3 py-2" type="date">
          <input v-model="holidayForm.holiday_name" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Ten ngay le">
          <label class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2">
            <input v-model="holidayForm.is_paid_leave" type="checkbox">
            Co luong
          </label>
          <button class="rounded-lg bg-blue-600 px-4 py-2 text-white">Them ngay le</button>
        </form>

        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Ngay</th>
                <th class="p-2">Ten</th>
                <th class="p-2">Loai</th>
                <th class="p-2">Tac vu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in holidays" :key="item.id" class="border-t">
                <td class="p-2">{{ item.holiday_date }}</td>
                <td class="p-2">{{ item.holiday_name }}</td>
                <td class="p-2">{{ item.is_paid_leave ? 'Paid' : 'Unpaid' }}</td>
                <td class="p-2">
                  <button class="rounded border px-2 py-1 text-red-600" type="button" @click="deleteHoliday(item.id)">Xoa</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Phan ca</h3>
        <form class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-5" @submit.prevent="submitAssignment">
          <select v-model="assignmentForm.employee_profile_id" class="rounded-lg border border-gray-300 px-3 py-2">
            <option :value="null">Nhan vien</option>
            <option v-for="item in employeeOptions" :key="item.id" :value="item.id">{{ item.label }}</option>
          </select>
          <select v-model="assignmentForm.department_id" class="rounded-lg border border-gray-300 px-3 py-2">
            <option :value="null">Phong ban</option>
            <option v-for="item in departmentOptions" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select>
          <select v-model="assignmentForm.work_shift_id" class="rounded-lg border border-gray-300 px-3 py-2">
            <option :value="null">Ca lam</option>
            <option v-for="item in workShifts" :key="item.id" :value="item.id">{{ item.shift_name }}</option>
          </select>
          <input v-model="assignmentForm.effective_from" class="rounded-lg border border-gray-300 px-3 py-2" type="date">
          <button class="rounded-lg bg-blue-600 px-4 py-2 text-white">Them phan ca</button>
        </form>

        <div class="mt-4 overflow-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Nhan vien/Phong ban</th>
                <th class="p-2">Ca</th>
                <th class="p-2">Hieu luc</th>
                <th class="p-2">Trang thai</th>
                <th class="p-2">Tac vu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in assignments" :key="item.id" class="border-t">
                <td class="p-2">{{ item.employee_name || item.department_name || '-' }}</td>
                <td class="p-2">{{ item.work_shift_name }}</td>
                <td class="p-2">{{ item.effective_from }} - {{ item.effective_to || 'Open' }}</td>
                <td class="p-2">{{ item.is_active ? 'Active' : 'Inactive' }}</td>
                <td class="p-2">
                  <button class="rounded border px-2 py-1" type="button" @click="toggleAssignment(item.id)">Toggle</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

defineProps({
  workShifts: { type: Array, default: () => [] },
  holidays: { type: Array, default: () => [] },
  assignments: { type: Array, default: () => [] },
  employeeOptions: { type: Array, default: () => [] },
  departmentOptions: { type: Array, default: () => [] },
})

const shiftForm = useForm({
  shift_name: '',
  start_time: '08:00',
  end_time: '17:30',
  standard_minutes: 480,
})

const holidayForm = useForm({
  holiday_date: '',
  holiday_name: '',
  is_paid_leave: true,
})

const assignmentForm = useForm({
  employee_profile_id: null,
  department_id: null,
  work_shift_id: null,
  effective_from: '',
})

const submitShift = () => shiftForm.post(route('attendance.catalogs.work-shifts.store'))
const submitHoliday = () => holidayForm.post(route('attendance.catalogs.holidays.store'))
const submitAssignment = () => assignmentForm.post(route('attendance.catalogs.assignments.store'))

const toggleShift = (id) => router.put(route('attendance.catalogs.work-shifts.toggle', id))
const deleteHoliday = (id) => router.delete(route('attendance.catalogs.holidays.destroy', id))
const toggleAssignment = (id) => router.put(route('attendance.catalogs.assignments.toggle', id))
</script>

