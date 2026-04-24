<template>
    <AdminLayout title="Duyệt yêu cầu nhân sự">
        <PageBreadcrumb title="Duyệt yêu cầu nhân sự" :items="breadcrumbItems" />

        <div class="mb-4 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="text-xs text-gray-500">Tổng yêu cầu</div>
                <div class="text-2xl font-semibold text-gray-900">{{ stats.total || 0 }}</div>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="text-xs text-amber-700">Chờ duyệt</div>
                <div class="text-2xl font-semibold text-amber-800">{{ stats.pending || 0 }}</div>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="text-xs text-emerald-700">Đã duyệt</div>
                <div class="text-2xl font-semibold text-emerald-800">{{ stats.approved || 0 }}</div>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                <div class="text-xs text-rose-700">Từ chối</div>
                <div class="text-2xl font-semibold text-rose-800">{{ stats.rejected || 0 }}</div>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Loại yêu cầu</label>
                    <select
                        :value="filters.request_type || ''"
                        @change="applyFilter({ request_type: $event.target.value, page: 1 })"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                    >
                        <option value="">Tất cả</option>
                        <option value="user_create">Tạo tài khoản</option>
                        <option value="user_salary_change">Đổi lương cơ bản</option>
                    </select>
                </div>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Loại yêu cầu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Thông tin</th>
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
                                <template v-if="item.request_type === 'user_create'">
                                    <div class="font-semibold text-gray-900">{{ item.payload.name || '-' }}</div>
                                    <div>{{ item.payload.email || '-' }}</div>
                                    <div>{{ item.payload.department_name || '-' }}</div>
                                </template>
                                <template v-else>
                                    <div class="font-semibold text-gray-900">{{ item.payload.employee_name || '-' }}</div>
                                    <div>{{ item.payload.employee_code || '-' }}</div>
                                    <div>{{ formatCurrency(item.payload.old_salary) }} -> {{ formatCurrency(item.payload.new_salary) }}</div>
                                </template>
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
                                    <button
                                        v-if="item.status === 'pending' && item.requested_by?.id === currentUserId"
                                        type="button"
                                        class="rounded-lg bg-gray-700 px-3 py-2 text-sm font-medium text-white"
                                        @click="cancel(item)"
                                    >
                                        Hủy
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
            title="Chi tiết yêu cầu"
            @close="selectedRequest = null"
            :custom_class="['relative', 'w-full', 'max-w-[760px]', 'rounded-xl', 'bg-white', 'shadow-2xl']"
        >
            <template #body>
                <div class="max-h-[80vh] overflow-y-auto px-6 pb-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                            <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin yêu cầu</div>
                            <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 md:grid-cols-2">
                                <div><span class="font-medium text-gray-900">Loại:</span> {{ requestTypeLabel(selectedRequest.request_type) }}</div>
                                <div><span class="font-medium text-gray-900">Trạng thái:</span> {{ statusLabel(selectedRequest.status) }}</div>
                                <div><span class="font-medium text-gray-900">Người gửi:</span> {{ selectedRequest.requested_by?.name || '-' }}</div>
                                <div><span class="font-medium text-gray-900">Gửi lúc:</span> {{ formatDateTime(selectedRequest.submitted_at) }}</div>
                            </div>
                        </div>

                        <template v-if="selectedRequest.request_type === 'user_create'">
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="mb-3 text-sm font-semibold text-gray-900">Tài khoản đề nghị</div>
                                <div class="space-y-2 text-sm text-gray-700">
                                    <div><span class="font-medium text-gray-900">Họ tên:</span> {{ selectedRequest.payload.name || '-' }}</div>
                                    <div><span class="font-medium text-gray-900">Email:</span> {{ selectedRequest.payload.email || '-' }}</div>
                                    <div><span class="font-medium text-gray-900">Điện thoại:</span> {{ selectedRequest.payload.phone || '-' }}</div>
                                </div>
                            </div>
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin nhân sự</div>
                                <div class="space-y-2 text-sm text-gray-700">
                                    <div><span class="font-medium text-gray-900">Phòng ban:</span> {{ selectedRequest.payload.department_name || '-' }}</div>
                                    <div><span class="font-medium text-gray-900">Chức vụ:</span> {{ selectedRequest.payload.position_name || '-' }}</div>
                                    <div><span class="font-medium text-gray-900">Ngày vào làm:</span> {{ formatDate(selectedRequest.payload.hire_date) }}</div>
                                    <div><span class="font-medium text-gray-900">Lương cơ bản:</span> {{ formatCurrency(selectedRequest.payload.base_salary) }}</div>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                                <div class="mb-3 text-sm font-semibold text-gray-900">Chi tiết đổi lương</div>
                                <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 md:grid-cols-2">
                                    <div><span class="font-medium text-gray-900">Nhân sự:</span> {{ selectedRequest.payload.employee_name || '-' }}</div>
                                    <div><span class="font-medium text-gray-900">Mã NV:</span> {{ selectedRequest.payload.employee_code || '-' }}</div>
                                    <div><span class="font-medium text-gray-900">Lương hiện tại:</span> {{ formatCurrency(selectedRequest.payload.old_salary) }}</div>
                                    <div><span class="font-medium text-gray-900">Lương đề nghị:</span> {{ formatCurrency(selectedRequest.payload.new_salary) }}</div>
                                    <div><span class="font-medium text-gray-900">Hiệu lực:</span> {{ formatDate(selectedRequest.payload.effective_date) }}</div>
                                    <div><span class="font-medium text-gray-900">Đơn vị:</span> {{ selectedRequest.payload.currency || 'VND' }}</div>
                                </div>
                                <div class="mt-3 text-sm text-gray-700">
                                    <span class="font-medium text-gray-900">Lý do:</span> {{ selectedRequest.payload.request_reason || selectedRequest.reason || '-' }}
                                </div>
                            </div>
                        </template>

                        <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                            <div class="mb-3 text-sm font-semibold text-gray-900">Nội dung thay đổi trước / sau</div>
                            <div v-if="selectedRequest.changes?.length" class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-xs uppercase text-gray-500">
                                            <th class="px-2 py-2">Trường</th>
                                            <th class="px-2 py-2">Trước</th>
                                            <th class="px-2 py-2">Sau</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="change in selectedRequest.changes" :key="change.field" class="border-t border-gray-100">
                                            <td class="px-2 py-2 font-medium text-gray-800">{{ change.label }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ displayValue(change.field, change.old_value) }}</td>
                                            <td class="px-2 py-2 text-gray-900">{{ displayValue(change.field, change.new_value) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-sm text-gray-500">Không có dữ liệu thay đổi.</div>
                        </div>
                    </div>
                </div>
            </template>
        </CustomModal>
        <ActionDialog ref="actionDialogRef" />
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Pagination from '@/components/tables/Pagination.vue'
import CustomModal from '@/components/modals/CustomModal.vue'
import ActionDialog from '@/components/ui/ActionDialog.vue'
import { useActionDialog } from '@/composables/useActionDialog'

const props = defineProps({
    approvalRequests: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({}) },
})

const breadcrumbItems = [
    { text: 'HCNS', link: null },
    { text: 'Duyệt nhân sự', link: null },
]

const selectedRequest = ref(null)
const page = usePage()
const currentUserId = page.props.auth?.user?.id
const { actionDialogRef, openPrompt } = useActionDialog()

const requestTypeLabel = (type) => ({
    user_create: 'Tạo tài khoản',
    user_salary_change: 'Đổi lương cơ bản',
}[type] || '-')

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

const formatDate = (value) => value ? new Date(value).toLocaleDateString('vi-VN') : '-'
const formatDateTime = (value) => value ? new Date(value).toLocaleString('vi-VN') : '-'

const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '-'
    return `${new Intl.NumberFormat('vi-VN').format(Number(value))} VND`
}

const displayValue = (field, value) => {
    if (value === null || value === undefined || value === '') return '-'
    if (['old_salary', 'new_salary', 'base_salary'].includes(field)) return formatCurrency(value)
    if (field === 'effective_date') return formatDate(value)
    if (typeof value === 'boolean') return value ? 'Có' : 'Không'
    if (typeof value === 'number') return String(value)
    if (Array.isArray(value)) return value.join(', ')
    return String(value)
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

const approve = async (item) => {
    const reviewNote = await openPrompt({
        title: 'Duyệt yêu cầu nhân sự',
        message: 'Nhập ghi chú duyệt nếu cần.',
        inputLabel: 'Ghi chú duyệt',
        inputType: 'textarea',
        defaultValue: '',
        okText: 'Duyệt',
        cancelText: 'Đóng',
        variant: 'primary',
        eyebrow: 'Nhân sự',
    })

    if (reviewNote === null) {
        return
    }

    router.post(route('web.user-approvals.approve', item.id), { review_note: reviewNote }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã duyệt yêu cầu.'),
        onError: () => toast.error('Không thể duyệt yêu cầu.'),
    })
}

const reject = async (item) => {
    const reviewNote = await openPrompt({
        title: 'Từ chối yêu cầu nhân sự',
        message: 'Nhập lý do từ chối.',
        inputLabel: 'Lý do từ chối',
        inputType: 'textarea',
        defaultValue: '',
        okText: 'Từ chối',
        cancelText: 'Đóng',
        variant: 'danger',
        eyebrow: 'Nhân sự',
    })

    if (reviewNote === null) {
        return
    }

    router.post(route('web.user-approvals.reject', item.id), { review_note: reviewNote }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã từ chối yêu cầu.'),
        onError: () => toast.error('Không thể từ chối yêu cầu.'),
    })
}

const cancel = async (item) => {
    const reviewNote = await openPrompt({
        title: 'Hủy yêu cầu nhân sự',
        message: 'Nhập ghi chú hủy nếu cần.',
        inputLabel: 'Ghi chú hủy',
        inputType: 'textarea',
        defaultValue: '',
        okText: 'Hủy yêu cầu',
        cancelText: 'Đóng',
        variant: 'warning',
        eyebrow: 'Nhân sự',
    })
    if (reviewNote === null) {
        return
    }

    router.post(route('web.user-approvals.cancel', item.id), { review_note: reviewNote }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã hủy yêu cầu.'),
        onError: () => toast.error('Không thể hủy yêu cầu.'),
    })
}
</script>
