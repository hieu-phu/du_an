<template>
    <AdminLayout title="Duyệt tài khoản">
        <PageBreadcrumb title="Duyệt tài khoản" :items="breadcrumbItems" />

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-[240px_180px]">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select
                        :value="filters.status || ''"
                        @change="applyFilter({ status: $event.target.value, page: 1 })"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                    >
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ duyệt</option>
                        <option value="approved">Đã duyệt</option>
                        <option value="rejected">Từ chối</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Số dòng / trang</label>
                    <select
                        :value="approvalRequests.per_page"
                        @change="applyFilter({ per_page: Number($event.target.value), page: 1 })"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                    >
                        <option :value="10">10</option>
                        <option :value="15">15</option>
                        <option :value="25">25</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Người gửi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tài khoản đề nghị</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Công việc</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Gửi lúc</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Trạng thái</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="item in approvalRequests.data" :key="item.id" class="align-top">
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div class="font-semibold text-gray-900">{{ item.requested_by?.name || '-' }}</div>
                                <div>{{ item.requested_by?.email || '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div class="font-semibold text-gray-900">{{ item.payload.name || '-' }}</div>
                                <div>{{ item.payload.email || '-' }}</div>
                                <div>{{ item.payload.phone || '-' }}</div>
                                <div class="mt-1 inline-flex rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">
                                    {{ item.payload.role_label || '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div>{{ item.payload.department_name || '-' }}</div>
                                <div>{{ item.payload.position_name || '-' }}</div>
                                <div class="text-xs text-gray-500">Vào làm: {{ formatDate(item.payload.hire_date) }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div>{{ formatDateTime(item.submitted_at) }}</div>
                                <div v-if="item.reviewed_at" class="mt-1 text-xs text-gray-500">
                                    Xử lý: {{ formatDateTime(item.reviewed_at) }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span :class="statusClass(item.status)" class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ statusLabel(item.status) }}
                                </span>
                                <div v-if="item.review_note" class="mt-2 max-w-xs text-xs text-gray-500">
                                    {{ item.review_note }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700"
                                        @click="openDetail(item)"
                                    >
                                        Xem
                                    </button>
                                    <button
                                        v-if="item.status === 'pending'"
                                        type="button"
                                        class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white"
                                        @click="approve(item)"
                                    >
                                        Duyệt
                                    </button>
                                    <button
                                        v-if="item.status === 'pending'"
                                        type="button"
                                        class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-medium text-white"
                                        @click="reject(item)"
                                    >
                                        Từ chối
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!approvalRequests.data.length">
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                Không có yêu cầu nào.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <Pagination :meta="approvalRequests" @page-change="handlePageChange" @items-per-page-change="handleItemsPerPageChange" />
        </div>

        <CustomModal
            v-if="selectedRequest"
            title="Chi tiết yêu cầu tạo tài khoản"
            @close="selectedRequest = null"
            :custom_class="['relative', 'w-full', 'max-w-[760px]', 'rounded-xl', 'bg-white', 'shadow-2xl']"
        >
            <template #body>
                <div class="max-h-[80vh] overflow-y-auto px-6 pb-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin tài khoản</div>
                            <div class="space-y-2 text-sm text-gray-700">
                                <div><span class="font-medium text-gray-900">Họ tên:</span> {{ selectedRequest.payload.name || '-' }}</div>
                                <div><span class="font-medium text-gray-900">Email:</span> {{ selectedRequest.payload.email || '-' }}</div>
                                <div><span class="font-medium text-gray-900">Số điện thoại:</span> {{ selectedRequest.payload.phone || '-' }}</div>
                                <div><span class="font-medium text-gray-900">Vai trò:</span> {{ selectedRequest.payload.role_label || '-' }}</div>
                                <div><span class="font-medium text-gray-900">Trạng thái TK:</span> {{ statusLabel(selectedRequest.payload.status) }}</div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin nhân sự</div>
                            <div class="space-y-2 text-sm text-gray-700">
                                <div><span class="font-medium text-gray-900">Phòng ban:</span> {{ selectedRequest.payload.department_name || '-' }}</div>
                                <div><span class="font-medium text-gray-900">Chức vụ:</span> {{ selectedRequest.payload.position_name || '-' }}</div>
                                <div><span class="font-medium text-gray-900">Ngày vào làm:</span> {{ formatDate(selectedRequest.payload.hire_date) }}</div>
                                <div><span class="font-medium text-gray-900">Loại nhân sự:</span> {{ employmentTypeLabel(selectedRequest.payload.employment_type) }}</div>
                                <div><span class="font-medium text-gray-900">Lương cơ bản:</span> {{ formatCurrency(selectedRequest.payload.base_salary) }}</div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                            <div class="mb-3 text-sm font-semibold text-gray-900">Địa chỉ</div>
                            <div class="space-y-2 text-sm text-gray-700">
                                <div>{{ fullAddress(selectedRequest.payload) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </CustomModal>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Pagination from '@/components/tables/Pagination.vue'
import CustomModal from '@/components/modals/CustomModal.vue'

const props = defineProps({
    approvalRequests: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
})

const breadcrumbItems = [
    { text: 'HCNS', link: null },
    { text: 'Duyệt tài khoản', link: null },
]

const selectedRequest = ref(null)

const statusLabel = (status) => ({
    active: 'Hoạt động',
    inactive: 'Ngừng hoạt động',
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    rejected: 'Từ chối',
    blocked: 'Đã khóa',
    cancelled: 'Đã hủy',
}[status] || '-')

const statusClass = (status) => ({
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-rose-100 text-rose-800',
    cancelled: 'bg-gray-100 text-gray-700',
}[status] || 'bg-gray-100 text-gray-700')

const employmentTypeLabel = (type) => ({
    probation: 'Thử việc',
    official: 'Chính thức',
    intern: 'Thực tập',
    collaborator: 'Cộng tác viên',
}[type] || '-')

const formatDate = (value) => value ? new Date(value).toLocaleDateString('vi-VN') : '-'
const formatDateTime = (value) => value ? new Date(value).toLocaleString('vi-VN') : '-'

const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '-'
    return `${new Intl.NumberFormat('vi-VN').format(Number(value))} VND`
}

const fullAddress = (payload) => {
    const parts = [
        payload.address_line,
        payload.ward_name,
        payload.province_name,
        payload.address,
    ].filter(Boolean)

    return parts.length ? parts.join(', ') : '-'
}

const applyFilter = (params = {}) => {
    router.get(route('web.user-approvals.index'), { ...props.filters, ...params }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const handlePageChange = (page) => applyFilter({ page })
const handleItemsPerPageChange = (perPage) => applyFilter({ per_page: perPage, page: 1 })

const openDetail = (item) => {
    selectedRequest.value = item
}

const approve = (item) => {
    const reviewNote = window.prompt('Ghi chú duyệt (có thể bỏ trống):', '')

    if (reviewNote === null) {
        return
    }

    router.post(route('web.user-approvals.approve', item.id), { review_note: reviewNote }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã duyệt yêu cầu tạo tài khoản.'),
        onError: () => toast.error('Không thể duyệt yêu cầu.'),
    })
}

const reject = (item) => {
    const reviewNote = window.prompt('Lý do từ chối:', '')

    if (reviewNote === null) {
        return
    }

    router.post(route('web.user-approvals.reject', item.id), { review_note: reviewNote }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã từ chối yêu cầu.'),
        onError: () => toast.error('Không thể từ chối yêu cầu.'),
    })
}
</script>
