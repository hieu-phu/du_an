<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Pagination from '@/components/tables/Pagination.vue'
import Modal from '@/components/ui/Modal.vue'

const props = defineProps({
    approvalRequests: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
})

const selectedRequest = ref(null)

const statusLabel = (status) => ({
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    rejected: 'Từ chối',
}[status] || '-')

const statusClass = (status) => ({
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-rose-100 text-rose-800',
}[status] || 'bg-gray-100 text-gray-700')

const requestTypeLabel = (type) => ({
    department_create: 'Tạo mới',
    department_update: 'Cập nhật',
    department_toggle: 'Khóa / mở',
}[type] || '-')

const formatDateTime = (value) => value ? new Date(value).toLocaleString('vi-VN') : '-'

const applyFilter = (params = {}) => {
    router.get(route('web.department-approvals.index'), { ...props.filters, ...params }, {
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

    router.post(route('web.department-approvals.approve', item.id), { review_note: reviewNote }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã duyệt yêu cầu phòng ban.'),
        onError: () => toast.error('Không thể duyệt yêu cầu phòng ban.'),
    })
}

const reject = (item) => {
    const reviewNote = window.prompt('Lý do từ chối:', '')

    if (reviewNote === null) {
        return
    }

    router.post(route('web.department-approvals.reject', item.id), { review_note: reviewNote }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã từ chối yêu cầu phòng ban.'),
        onError: () => toast.error('Không thể từ chối yêu cầu phòng ban.'),
    })
}
</script>

<template>
    <AdminLayout>
        <PageBreadcrumb
            title="Duyệt phòng ban"
            :items="[
                { text: 'HCNS', link: null },
                { text: 'Duyệt phòng ban', link: null },
            ]"
        />

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
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Loại yêu cầu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Phòng ban</th>
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
                                <div class="font-semibold text-gray-900">{{ requestTypeLabel(item.request_type) }}</div>
                                <div class="text-xs text-gray-500">{{ item.reason || '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div class="font-semibold text-gray-900">{{ item.payload.name || item.department?.name || '-' }}</div>
                                <div>Trưởng phòng: {{ item.payload.manager_name || '-' }}</div>
                                <div class="text-xs text-gray-500">Trạng thái mới: {{ item.payload.is_active_label || '-' }}</div>
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
                                Không có yêu cầu phòng ban nào.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <Pagination :meta="approvalRequests" @page-change="handlePageChange" @items-per-page-change="handleItemsPerPageChange" />
        </div>

        <Modal :show="!!selectedRequest" @close="selectedRequest = null">
            <div v-if="selectedRequest" class="p-6">
                <h2 class="mb-5 text-lg font-semibold text-gray-900">Chi tiết yêu cầu phòng ban</h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin yêu cầu</div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div><span class="font-medium text-gray-900">Loại yêu cầu:</span> {{ requestTypeLabel(selectedRequest.request_type) }}</div>
                            <div><span class="font-medium text-gray-900">Người gửi:</span> {{ selectedRequest.requested_by?.name || '-' }}</div>
                            <div><span class="font-medium text-gray-900">Gửi lúc:</span> {{ formatDateTime(selectedRequest.submitted_at) }}</div>
                            <div><span class="font-medium text-gray-900">Trạng thái:</span> {{ statusLabel(selectedRequest.status) }}</div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin phòng ban</div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div><span class="font-medium text-gray-900">Tên phòng ban:</span> {{ selectedRequest.payload.name || selectedRequest.department?.name || '-' }}</div>
                            <div><span class="font-medium text-gray-900">Trưởng phòng:</span> {{ selectedRequest.payload.manager_name || '-' }}</div>
                            <div><span class="font-medium text-gray-900">Trạng thái sau duyệt:</span> {{ selectedRequest.payload.is_active_label || '-' }}</div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Mô tả</div>
                        <div class="text-sm text-gray-700">
                            {{ selectedRequest.payload.description || 'Không có mô tả.' }}
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
