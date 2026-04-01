<template>
    <AdminLayout title="Danh sách nhân sự">
        <PageBreadcrumb :title="title" :items="[{ text: 'HCNS', link: '/hcns' }, { text: 'Nhân sự' }]" />

        <!-- Filter & Add Button -->
        <div class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 p-4">
                <div class="w-full flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3 mb-2">
                    <Button @click="openAddUserModal" size="md" variant="primary" :startIcon="AddIcon"
                        :title="'Thêm Nhân sự mới'" class="w-full sm:w-auto whitespace-nowrap">
                        Nhân sự
                    </Button>
                </div>
                <div class="w-full h-full min-w-0 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                    <SearchPage :filters="filterConfig" @filter="applyFilter" />
                </div>
            </div>
        </div>

        <!-- DataTable chính -->
        <DataTable :columns="columns" :data="users.data" :loading="false" :showIndex="true"
            :indexOffset="(users.current_page - 1) * users.per_page" :actions="actions"
            emptyMessage="Không có dữ liệu nhân sự">
            <!-- Avatar + Tên + Username -->
            <template #cell-user="{ item }">
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        <!-- Ảnh avatar -->
                        <img v-if="item.avatar" :src="avatarUrl(item.avatar)" :alt="item.name"
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700"
                            @error="handleImageError" />
                        <!-- Placeholder nếu không có ảnh -->
                        <div v-else
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                            {{ getInitials(item.name) }}
                        </div>

                        <!-- Badge Nhân sự chính thức -->
                        <span v-if="item.is_employment"
                            class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"
                            title="Nhân sự chính thức"></span>
                    </div>

                    <div>
                        <div class="font-medium text-gray-900 dark:text-white ">{{ item.name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@{{ item.username }}</div>
                    </div>
                </div>
            </template>

            <!-- Liên hệ -->
            <template #cell-contact="{ item }">
                <div class="space-y-1 text-sm">
                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate max-w-[180px]">{{ item.email }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        {{ item.phone || '-' }}
                    </div>
                </div>
            </template>

            <!-- Công ty -->
            <template #cell-companies="{ item }">
                <div v-if="item.companies?.length" class="flex flex-wrap gap-1">
                    <span v-for="c in item.companies.slice(0, 2)" :key="c.id"
                        class="px-2 py-1 text-xs font-medium rounded-full text-blue-800 whitespace-nowrap">
                        {{ c.name }}
                    </span>
                    <span v-if="item.companies.length > 2"
                        class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        +{{ item.companies.length - 2 }}
                    </span>
                </div>
                <span v-else class="text-sm text-gray-400">-</span>
            </template>

            <!-- Phòng ban -->
            <template #cell-department="{ item }">
                <div v-if="item.department" class="space-y-1">
                    <div class="items-center gap-2">
                        <span
                            class="inline-flex items-center px-2.5 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                            {{ item.department.name }}
                        </span>
                        <br />
                        <span v-if="item.position"
                            class="text-sm ps-2 text-gray-600 dark:text-gray-400 font-medium whitespace-nowrap">
                            {{ item.position.name }}
                        </span>
                    </div>
                </div>
                <span v-else class="text-sm text-gray-400">-</span>
            </template>



            <!-- Trạng thái -->
            <template #cell-status="{ item }">
                <span :class="getStatusClass(item.status)"
                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(item.status)"></span>
                    {{ getStatusText(item.status) }}
                </span>
            </template>

            <!-- Đăng nhập lần cuối -->
            <template #cell-last_login="{ item }">
                <div v-if="item.last_login_at" class="text-center text-sm">
                    <div class="text-gray-700 dark:text-gray-300">{{ formatDate(item.last_login_at) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatTime(item.last_login_at) }}</div>
                </div>
                <span v-else class="text-sm text-gray-400">Chưa đăng nhập</span>
            </template>
        </DataTable>

        <!-- Pagination -->
        <div class="mt-6">
            <Pagination :meta="users" @page-change="handlePageChange"
                @items-per-page-change="handleItemsPerPageChange" />
        </div>

        <!-- Modal thêm/sửa Nhân sự -->
        <UserFormModal v-model="isUserModalOpen" :is-edit-mode="isUserEditMode"
            :user-data="selectedUser" :store-route="route('web.users.store')"
            :update-route="route('web.users.update', ':id')" @success="handleModalSuccess" />
    </AdminLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'

// Components
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchPage from '@/components/features/SearchPage.vue'
import Button from '@/components/ui/Button.vue'
import DataTable from '@/components/tables/DataTable.vue'
import Pagination from '@/components/tables/Pagination.vue'
import UserFormModal from '@/components/users/UserFormModal.vue'
import { toast } from 'vue3-toastify'

// Icons
import AddIcon from '@/icons/AddIcon.vue'
import EditButtonIcon from '@/icons/EditButtonIcon.vue'
import Lock from '@/icons/Lock.vue'
import Unlock from '@/icons/Unlock.vue'

const props = defineProps({
    users: Object,
    filters: Object,
})

const title = 'Danh sách nhân sự'
const isUserModalOpen = ref(false)
const isUserEditMode = ref(false)
const selectedUser = ref(null)
// Cột bảng
const columns = [
    { label: 'Nhân sự', key: 'user', width: '200px' },
    { label: 'Liên hệ', key: 'contact', width: '240px' },
    { label: 'Công ty', key: 'companies' },
    { label: 'Phòng ban', key: 'department' },
    { label: 'Trạng thái', key: 'status', align: 'text-center', width: '140px' }
]

// Các nút thao tác
const actions = [
    // Sửa
    {
        icon: EditButtonIcon,
        buttonProps: { class: 'mr-3 devc__admin__action-btn devc-btn-edit', title: 'Chỉnh sửa' },
        onClick: (item) => openEditUserModal(item)
    },
    // Bật/tắt trạng thái
    {
        icon: (item) => item.status === 'active' ? Lock : Unlock,
        buttonProps: { class: 'devc__admin__action-btn devc-btn-view', title: 'Trạng thái' },
        onClick: (item) => toggleUserStatus(item)
    }
]

// Bộ lọc
const filterConfig = computed(() => [
    { label: 'Tìm kiếm nhân sự', name: 'search', type: 'text', placeholder: 'Tên, email, số điện thoại...', value: props.filters?.search ?? '' },
    {
        label: 'Trạng thái', name: 'status', type: 'select', options: [
            { value: '', label: 'Tất cả trạng thái' },
            { value: 'active', label: 'Đang hoạt động' },
            { value: 'inactive', label: 'Ngừng hoạt động' },
            { value: 'pending', label: 'Đang chờ' },
            { value: 'blocked', label: 'Đã khóa' }
        ], value: props.filters?.status ?? ''
    }
])

// Helper functions
const getInitials = (name = '') => {
    const parts = name.trim().split(' ')
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase()
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}



const getStatusClass = (status) => ({
    active: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    blocked: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
}[status] || 'bg-gray-100 text-gray-800')

const getStatusDotClass = (status) => ({
    active: 'bg-green-500',
    inactive: 'bg-gray-400',
    pending: 'bg-yellow-500',
    blocked: 'bg-red-500'
}[status] || 'bg-gray-400')

const getStatusText = (status) => ({
    active: 'Hoạt động',
    inactive: 'Không hoạt động',
    pending: 'Đang chờ',
    blocked: 'Đã khóa'
}[status] || status)

const formatDate = (d) => d ? new Date(d).toLocaleDateString('vi-VN') : '-'
const formatTime = (d) => d ? new Date(d).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : '-';

const toggleUserStatus = (user) => {
    router.put(route('web.users.toggle', user.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Cập nhật trạng thái thành công!')
        },
        onError: () => {
            toast.error('Có lỗi xảy ra khi cập nhật trạng thái!')
        }
    })
}

// Hàm trả về URL ảnh đúng cách Laravel
const avatarUrl = (filename) => {
    if (!filename) return ''
    // Nếu đã có http:// hoặc https:// thì trả luôn (trường hợp dùng CDN)
    if (filename.startsWith('http')) return filename
    return `/storage/${filename}`
}

// Fallback khi ảnh lỗi (403/404)
const handleImageError = (e) => {
    e.target.style.display = 'none'
    e.target.nextElementSibling?.classList.remove('hidden')
}

const applyFilter = (filters) => {
    router.get(route('web.users.index'), filters, { preserveState: true, preserveScroll: true })
}


const handlePageChange = (page) => {
    router.get(route('web.users.index'), { ...props.filters, page }, { preserveState: true, preserveScroll: true })
}

const handleItemsPerPageChange = (perPage) => {
    router.get(route('web.users.index'), { ...props.filters, per_page: perPage, page: 1 }, { preserveState: true, preserveScroll: true })
}


const openAddUserModal = () => {
    isUserEditMode.value = false
    selectedUser.value = null
    isUserModalOpen.value = true
}

const openEditUserModal = async (user) => {
    isUserEditMode.value = true
    // Reset về null trước để Vue luôn detect thay đổi
    // dù click cùng một user nhiều lần liên tiếp
    selectedUser.value = null
    await nextTick()
    selectedUser.value = user
    isUserModalOpen.value = true
}

const handleModalSuccess = () => {
    // Có thể thêm logic xử lý sau khi thành công nếu cần
    // Ví dụ: refresh data, show notification, etc.
}
</script>

<style scoped>
/* Nếu bạn dùng Modal component có class no-scrollbar */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>