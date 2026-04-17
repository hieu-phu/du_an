<template>
  <Head title="Duyet dieu chinh cong" />

  <AdminLayout>
    <PageBreadcrumb title="Duyet dieu chinh cong" :items="[{ text: 'Cham cong', link: null }, { text: 'Duyet dieu chinh', link: null }]" />

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-theme-sm">
      <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
        <select v-model="filterForm.month" class="rounded-lg border border-gray-300 px-3 py-2">
          <option v-for="month in filters.months" :key="month.value" :value="month.value">{{ month.label }}</option>
        </select>
        <select v-model="filterForm.year" class="rounded-lg border border-gray-300 px-3 py-2">
          <option v-for="year in filters.years" :key="year.value" :value="year.value">{{ year.label }}</option>
        </select>
        <select v-model="filterForm.employee_profile_id" class="rounded-lg border border-gray-300 px-3 py-2">
          <option :value="null">Tat ca nhan vien</option>
          <option v-for="item in employees" :key="item.id" :value="item.id">{{ item.label }}</option>
        </select>
        <button class="rounded-lg bg-blue-600 px-4 py-2 text-white" type="button" @click="applyFilter">Loc</button>
      </div>

      <div class="overflow-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left">
              <th class="p-2">Nhan vien</th>
              <th class="p-2">Ngay cong</th>
              <th class="p-2">Check in</th>
              <th class="p-2">Check out</th>
              <th class="p-2">Ly do</th>
              <th class="p-2">Tac vu</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in adjustments" :key="item.id" class="border-t">
              <td class="p-2">{{ item.employee_name }} ({{ item.employee_code }})</td>
              <td class="p-2">{{ item.work_date }}</td>
              <td class="p-2">{{ item.old_check_in_at || '-' }} -> {{ item.new_check_in_at || '-' }}</td>
              <td class="p-2">{{ item.old_check_out_at || '-' }} -> {{ item.new_check_out_at || '-' }}</td>
              <td class="p-2">{{ item.reason }}</td>
              <td class="p-2">
                <div class="flex gap-2">
                  <button class="rounded bg-emerald-600 px-3 py-1 text-white" type="button" @click="approve(item.id)">Duyet</button>
                  <button class="rounded bg-rose-600 px-3 py-1 text-white" type="button" @click="reject(item.id)">Tu choi</button>
                </div>
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
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const props = defineProps({
  filters: { type: Object, required: true },
  employees: { type: Array, default: () => [] },
  adjustments: { type: Array, default: () => [] },
})

const filterForm = reactive({
  month: props.filters.month,
  year: props.filters.year,
  employee_profile_id: props.filters.employee_profile_id ?? null,
})

const applyFilter = () => {
  router.get(route('attendance.adjustments.approvals'), {
    month: filterForm.month,
    year: filterForm.year,
    employee_profile_id: filterForm.employee_profile_id,
  }, { preserveState: true, preserveScroll: true })
}

const approve = (id) => {
  const note = window.prompt('Ghi chu duyet', '') ?? ''
  router.post(route('attendance.adjustments.approve', id), { note }, { preserveScroll: true })
}

const reject = (id) => {
  const note = window.prompt('Ly do tu choi', '') ?? ''
  router.post(route('attendance.adjustments.reject', id), { note }, { preserveScroll: true })
}
</script>

