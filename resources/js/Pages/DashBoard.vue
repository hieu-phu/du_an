<template>

  <Head title="Dashboard - Ecommerce"></Head>

  <AdminLayout>
    <PageBreadcrumb title="Dashboard" :items="[{ text: 'Dashboard', link: null }]" />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
      <!-- Metric Cards -->
      <div v-for="metric in metrics" :key="metric.title"
        class="rounded-lg border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-title-md font-bold text-gray-800 dark:text-white/90">
              {{ metric.value }}
            </h4>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ metric.title }}</span>
          </div>
          <div class="flex items-center justify-center w-12 h-12 rounded-full" :class="metric.bgColor">
            <component :is="metric.icon" class="text-white" />
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7.5">


    </div>

    <!-- Add your charts and tables here -->
    <DataTable :columns="columns" :data="users" :showIndex="true" :actions="actions"
      emptyMessage="Không có người dùng" />
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { LayoutDashboardIcon, UserGroupIcon, BoxIcon, BarChartIcon } from '@/icons'
import DataTable from '@/components/tables/DataTable.vue'
import { ref } from 'vue'
import EyeOn from '../icons/EyeOn.vue'
import EyeOff from '../icons/EyeOff.vue'
import EditButtonIcon from '../icons/EditButtonIcon.vue'
import axios from 'axios'

const metrics = [
  {
    title: 'Total Users',
    value: '1,234',
    icon: UserGroupIcon,
    bgColor: 'bg-brand-500'
  },
  {
    title: 'Total Orders',
    value: '567',
    icon: BoxIcon,
    bgColor: 'bg-success-500'
  },
  {
    title: 'Total Revenue',
    value: '$12,345',
    icon: BarChartIcon,
    bgColor: 'bg-warning-500'
  },
  {
    title: 'Total Products',
    value: '89',
    icon: LayoutDashboardIcon,
    bgColor: 'bg-error-500'
  }
];

const columns = [
  { label: 'ID', key: 'id', align: 'text-center', width: '80px' },
  { label: 'Tên', key: 'name' },
  { label: 'Email', key: 'email' }
]

const users = ref([
  { id: 1, name: 'Nguyễn Văn A', email: 'a@example.com', status: 'active' },
  { id: 2, name: 'Trần Thị B', email: 'b@example.com', status: 'inactive' }
])

const actions = [
  {
    icon: EditButtonIcon,
    buttonProps: { class: 'mr-3 devc__admin__action-btn' },
    onClick: (item) => openEditModal(item)
  },
  {
    // Không dùng icon là hàm nữa, thay bằng render thủ công
    buttonProps: { class: 'devc__admin__action-btn' },
    onClick: (item) => toggleStatus(item),
    // Thêm một custom render cho action này
    render: (item) => item.status === 'active' ? EyeOn : EyeOff
  }
]

// Hàm mẫu
function openEditModal(user) {
  alert('Chỉnh sửa ' + user.name)
}

function toggleStatus(user) {
  user.status = user.status === 'active' ? 'inactive' : 'active'
}
function getData() {
  axios.get('/api/user')
    .then(response => {
      console.log(response.data);
    })
    .catch(error => {
      console.error('Error fetching dashboard data:', error);
    });
}
import { getParamsURL } from '@/config/helpers';
const paramsObject = getParamsURL()
getData(paramsObject);
</script>


<style scoped>
.btn {
  padding: 4px 10px;
  border-radius: 4px;
  color: white;
  cursor: pointer;
}

.btn-primary {
  background-color: #3b82f6;
}

.btn-danger {
  background-color: #ef4444;
}

.btn-info {
  background-color: #0ea5e9;
}
</style>