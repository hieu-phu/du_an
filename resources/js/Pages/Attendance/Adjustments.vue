<template>
  <Head title="Dieu chinh cong" />

  <AdminLayout>
    <PageBreadcrumb title="Dieu chinh cong" :items="[{ text: 'Cham cong', link: null }, { text: 'Dieu chinh cong', link: null }]" />

    <div class="space-y-6">
      <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
        <h3 class="text-lg font-semibold text-gray-900">Gui yeu cau dieu chinh</h3>
        <form class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2" @submit.prevent="submit">
          <select v-model="form.attendance_record_id" class="rounded-lg border border-gray-300 px-3 py-2">
            <option :value="null">Chon ngay cong</option>
            <option v-for="item in records" :key="item.id" :value="item.id">
              {{ item.work_date }} - {{ item.employee_code || '' }}
            </option>
          </select>
          <input v-model="form.new_check_in_at" class="rounded-lg border border-gray-300 px-3 py-2" type="datetime-local" placeholder="Check in moi">
          <input v-model="form.new_check_out_at" class="rounded-lg border border-gray-300 px-3 py-2" type="datetime-local" placeholder="Check out moi">
          <textarea v-model="form.reason" class="rounded-lg border border-gray-300 px-3 py-2 md:col-span-2" rows="3" placeholder="Ly do dieu chinh" />
          <div class="md:col-span-2 flex justify-end">
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-white" :disabled="form.processing" type="submit">
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
                <th class="p-2">Check in cu/moi</th>
                <th class="p-2">Check out cu/moi</th>
                <th class="p-2">Trang thai</th>
                <th class="p-2">Ghi chu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in adjustments" :key="item.id" class="border-t">
                <td class="p-2">{{ item.work_date }}</td>
                <td class="p-2">{{ item.old_check_in_at || '-' }} -> {{ item.new_check_in_at || '-' }}</td>
                <td class="p-2">{{ item.old_check_out_at || '-' }} -> {{ item.new_check_out_at || '-' }}</td>
                <td class="p-2">{{ item.status }}</td>
                <td class="p-2">{{ item.review_note || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

defineProps({
  records: { type: Array, default: () => [] },
  adjustments: { type: Array, default: () => [] },
})

const form = useForm({
  attendance_record_id: null,
  new_check_in_at: '',
  new_check_out_at: '',
  reason: '',
})

const submit = () => {
  form.post(route('attendance.adjustments.store'))
}
</script>

