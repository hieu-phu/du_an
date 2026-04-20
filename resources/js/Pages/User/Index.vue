<template>
    <AdminLayout :title="title">
        <PageBreadcrumb :title="title" :items="breadcrumbItems" />

        <div class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 p-4">
                <div class="mb-2 flex w-full flex-col items-stretch justify-end gap-3 sm:flex-row sm:items-center">
                    <Button
                        v-if="permissions['users.create']"
                        @click="openAddUserModal"
                        size="md"
                        variant="primary"
                        :startIcon="AddIcon"
                        :title="createButtonTitle"
                        class="w-full whitespace-nowrap sm:w-auto"
                    >
                        {{ createButtonText }}
                    </Button>

                    <Button
                        v-if="permissions['users.approvals.view']"
                        @click="goToApprovals"
                        size="md"
                        variant="outline"
                        :startIcon="CheckCirleIcon"
                        class="w-full whitespace-nowrap sm:w-auto border-blue-600 text-blue-600 hover:bg-blue-50"
                    >
                        Duyệt tài khoản
                    </Button>

                    <Button
                        v-if="!canApproveRequests"
                        @click="goToRequests"
                        size="md"
                        variant="outline"
                        :startIcon="CheckCirleIcon"
                        class="w-full whitespace-nowrap sm:w-auto border-indigo-600 text-indigo-600 hover:bg-indigo-50"
                    >
                        Trạng thái yêu cầu
                    </Button>
                </div>

                <div class="h-full w-full min-w-0 border-t border-gray-100 pt-4 dark:border-gray-700/50">
                    <SearchPage :filters="filterConfig" @filter="applyFilter" />
                </div>
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="users.data"
            :loading="false"
            :showIndex="true"
            :indexOffset="(users.current_page - 1) * users.per_page"
            :actions="actions"
            :emptyMessage="pageKey === 'employees' ? 'Không có dữ liệu nhân sự' : 'Không có dữ liệu tài khoản'"
        >
            <template #cell-user="{ item }">
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        <img
                            v-if="item.avatar && !imageErrors[item.id]"
                            :src="avatarUrl(item.avatar)"
                            :alt="item.name"
                            class="h-10 w-10 rounded-full border-2 border-gray-200 object-cover dark:border-gray-700"
                            @error="imageErrors[item.id] = true"
                        />
                        <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-sm font-semibold text-white">
                            {{ getInitials(item.name) }}
                        </div>
                    </div>

                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ item.name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ item.employee_code || '-' }}</div>
                        <div class="text-xs text-indigo-600 dark:text-indigo-400">{{ getPositionSummary(item) }}</div>
                    </div>
                </div>
            </template>

            <template #cell-contact="{ item }">
                <div class="space-y-1 text-sm">
                    <div class="text-gray-700 dark:text-gray-300">{{ item.email }}</div>
                    <div class="text-gray-700 dark:text-gray-300">{{ item.phone || '-' }}</div>
                </div>
            </template>

            <template #cell-department="{ item }">
                <div v-if="item.department" class="space-y-1">
                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-blue-100 px-2.5 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                        {{ item.department.name }}
                    </span>
                    <div v-if="item.position" class="text-sm text-gray-600 dark:text-gray-400">
                        {{ item.position.name }}
                    </div>
                </div>
                <span v-else class="text-sm text-gray-400">-</span>
            </template>

            <template #cell-employment="{ item }">
                <div class="space-y-1 text-sm">
                    <div>{{ formatDate(item.hire_date) }}</div>
                    <div class="text-gray-500 dark:text-gray-400">{{ formatCurrency(item.base_salary) }}</div>
                    <div class="text-xs text-indigo-600 dark:text-indigo-400">{{ getEmploymentTypeText(item.employment_type) }}</div>
                </div>
            </template>

            <template #cell-status="{ item }">
                <div class="space-y-3">
                    <div>
                        <div class="mb-1 text-xs font-medium text-gray-500">Tài khoản</div>
                        <select
                            :value="item.status"
                            @change="changeAccountStatus(item, $event.target.value)"
                            :disabled="!canManageUser(item) || item.employment_status === 'terminated'"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:disabled:bg-gray-700"
                        >
                            <option value="active">Đang hoạt động</option>
                            <option value="inactive">Ngừng hoạt động</option>
                            <option value="pending">Đang chờ</option>
                            <option value="blocked">Đã khóa</option>
                        </select>
                    </div>

                    <div>
                        <div class="mb-1 text-xs font-medium text-gray-500">Trạng thái làm việc</div>
                        <select
                            :value="item.employment_status || 'active'"
                            @change="changeEmploymentStatus(item, $event.target.value)"
                            :disabled="!canManageUser(item)"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <option value="active">Đang làm việc</option>
                            <option value="inactive">Tạm ngừng</option>
                            <option value="terminated">Nghỉ việc</option>
                        </select>
                    </div>
                </div>
            </template>
        </DataTable>

        <div class="mt-6">
            <Pagination :meta="users" @page-change="handlePageChange" @items-per-page-change="handleItemsPerPageChange" />
        </div>

        <UserFormModal
            v-model="isUserModalOpen"
            :is-edit-mode="isUserEditMode"
            :user-data="selectedUser"
            :departments="departments"
            :positions="positions"
            :provinces="provinces"
            :store-route="route('web.users.store')"
            :update-route="route('web.users.update', ':id')"
            @success="handleModalSuccess"
        />

        <CustomModal
            v-if="isDetailModalOpen && selectedUser"
            :title="pageKey === 'employees' ? 'Chi tiết nhân sự' : 'Chi tiết tài khoản'"
            @close="closeDetailModal"
            :custom_class="detailModalClasses"
        >
            <template #body>
                <div class="max-h-[80vh] overflow-y-auto px-6 pb-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="mb-3 text-sm font-semibold text-gray-800">Thông tin tài khoản</div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div><span class="font-medium text-gray-800">Họ tên:</span> {{ selectedUser.name }}</div>
                                <div><span class="font-medium text-gray-800">Mã nhân viên:</span> {{ selectedUser.employee_code || '-' }}</div>
                                <div><span class="font-medium text-gray-800">Người tạo:</span> <span class="text-blue-600 font-semibold">{{ selectedUser.creator_name || 'Hệ thống' }}</span></div>
                                <div><span class="font-medium text-gray-800">Cấp bậc:</span> {{ getPositionSummary(selectedUser) }}</div>
                                <div><span class="font-medium text-gray-800">Email:</span> {{ selectedUser.email }}</div>
                                <div><span class="font-medium text-gray-800">Số điện thoại:</span> {{ selectedUser.phone || '-' }}</div>
                                <div><span class="font-medium text-gray-800">Trạng thái tài khoản:</span> {{ getStatusText(selectedUser.status) }}</div>
                                <div><span class="font-medium text-gray-800">Trạng thái làm việc:</span> {{ getEmploymentStatusText(selectedUser.employment_status) }}</div>
                                <div><span class="font-medium text-gray-800">Loại nhân sự:</span> {{ getEmploymentTypeText(selectedUser.employment_type) }}</div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="mb-3 text-sm font-semibold text-gray-800">Thông tin công việc</div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div><span class="font-medium text-gray-800">Phòng ban:</span> {{ selectedUser.department?.name || '-' }}</div>
                                <div><span class="font-medium text-gray-800">Chức vụ:</span> {{ selectedUser.position?.name || '-' }}</div>
                                <div><span class="font-medium text-gray-800">Ngày vào làm:</span> {{ formatDate(selectedUser.hire_date) }}</div>
                                <div><span class="font-medium text-gray-800">Ngày nghỉ việc:</span> {{ formatDate(selectedUser.termination_date) }}</div>
                                <div><span class="font-medium text-gray-800">Lương cơ bản:</span> {{ formatCurrency(selectedUser.base_salary) }}</div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                            <div class="mb-3 text-sm font-semibold text-gray-800">Quyền theo chức vụ</div>
                            <div class="space-y-3 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium text-gray-800">Mức quyền hạn:</span>
                                    {{ selectedUser.position?.authority_level ?? 'Chưa thiết lập' }}
                                </div>
                                <div v-if="!selectedUser.position?.capabilities?.length" class="rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                                    Chức vụ này chưa cấu hình capability.
                                </div>
                                <div v-else class="flex flex-wrap gap-2">
                                    <span
                                        v-for="capability in selectedUser.position.capabilities"
                                        :key="capability"
                                        class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700"
                                    >
                                        {{ getCapabilityLabel(capability) }}
                                    </span>
                                </div>
                            </div>
                        </div>                        <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                            <div class="mb-3 text-sm font-semibold text-gray-800">Ghi đè quyền theo cá nhân</div>
                            <div class="space-y-3 text-sm text-gray-600">
                                <div v-if="!selectedUser.position_capability_overrides?.length" class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                    Chưa có ghi đè. Người dùng đang dùng quyền theo chức vụ.
                                </div>
                                <div v-else class="space-y-2">
                                    <div
                                        v-for="override in selectedUser.position_capability_overrides"
                                        :key="override.id"
                                        :class="override.is_expired ? 'border-amber-200 bg-amber-50/60' : 'border-gray-200'"
                                        class="flex items-start justify-between gap-3 rounded-md border px-3 py-2"
                                    >
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 text-sm font-medium text-gray-800">
                                                {{ getCapabilityLabel(override.capability_code) }}
                                                <span class="text-xs text-gray-500">({{ override.capability_code }})</span>
                                                <span
                                                    v-if="override.is_expired"
                                                    class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800"
                                                >
                                                    Đã hết hạn
                                                </span>
                                            </div>
                                            <div class="mt-0.5 text-xs" :class="override.effect === 'allow' ? 'text-emerald-700' : 'text-rose-700'">
                                                {{ override.effect === 'allow' ? 'Cho phép' : 'Từ chối' }}
                                                <span v-if="override.expires_at" class="text-gray-500"> - hết hạn: {{ formatDate(override.expires_at) }}</span>
                                            </div>
                                            <div v-if="override.reason" class="mt-0.5 text-xs text-gray-500">
                                                Lý do: {{ override.reason }}
                                            </div>
                                        </div>
                                        <button
                                            v-if="canManageOverrides"
                                            type="button"
                                            class="rounded-md border border-rose-200 px-2 py-1 text-xs text-rose-700 hover:bg-rose-50"
                                            @click="removeOverride(override)"
                                        >
                                            Xóa
                                        </button>
                                    </div>
                                </div>

                                <div v-if="canManageOverrides" class="mt-2 rounded-md border border-blue-100 bg-blue-50/40 p-3">
                                    <div class="mb-2 text-xs font-semibold text-blue-700">Thêm / cập nhật ghi đè</div>
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                        <FormSelect
                                            v-model="overrideForm.capability_code"
                                            :options="capabilityOptionItems"
                                            label="Quyen"
                                            placeholder="Chon quyen"
                                            :searchable="true"
                                            :can-clear="true"
                                        />
                                        <FormSelect
                                            v-model="overrideForm.effect"
                                            :options="effectOptionItems"
                                            label="Hieu luc"
                                            placeholder="Chon hieu luc"
                                            :searchable="false"
                                            :can-clear="false"
                                        />
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-700">Hết hạn (nếu có)</label>
                                            <input v-model="overrideForm.expires_at" type="datetime-local" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="mb-1 block text-xs font-medium text-gray-700">Lý do</label>
                                            <input v-model="overrideForm.reason" type="text" placeholder="Nhập lý do (không bắt buộc)" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm" />
                                        </div>
                                    </div>
                                    <div class="mt-3 flex justify-end">
                                        <button
                                            type="button"
                                            class="rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700"
                                            @click="submitOverride"
                                        >
                                            Lưu ghi đè
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                            <div class="mb-3 text-sm font-semibold text-gray-800">Thông tin bổ sung</div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div><span class="font-medium text-gray-800">Ngày sinh:</span> {{ formatDate(selectedUser.date_of_birth) }}</div>
                                <div><span class="font-medium text-gray-800">Địa chỉ:</span> {{ formatFullAddress(selectedUser) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </CustomModal>
    </AdminLayout>
</template>

<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchPage from '@/components/features/SearchPage.vue'
import Button from '@/components/ui/Button.vue'
import DataTable from '@/components/tables/DataTable.vue'
import Pagination from '@/components/tables/Pagination.vue'
import UserFormModal from '@/components/users/UserFormModal.vue'
import CustomModal from '@/components/modals/CustomModal.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import { toast } from 'vue3-toastify'
import AddIcon from '@/icons/AddIcon.vue'
import EditButtonIcon from '@/icons/EditButtonIcon.vue'
import EyeOn from '@/icons/EyeOn.vue'
import CheckCirleIcon from '@/icons/CheckCirleIcon.vue'

const props = defineProps({
    users: Object,
    filters: Object,
    pageTitle: { type: String, default: 'Danh sách tài khoản' },
    pageKey: { type: String, default: 'accounts' },
    detailUser: { type: Object, default: null },
    departments: { type: Array, default: () => [] },
    positions: { type: Array, default: () => [] },
    capabilityOptions: { type: Array, default: () => [] },
    provinces: { type: Array, default: () => [] },
})

const page = usePage()
const permissions = computed(() => page.props.auth?.permissions || {})
const currentAuthorityLevel = computed(() => Number(page.props.auth?.user?.authority_level || 0))
const canApproveRequests = computed(() => page.props.auth?.position_capabilities?.approve_requests === true)

const title = props.pageTitle
const isUserModalOpen = ref(false)
const isUserEditMode = ref(false)
const isDetailModalOpen = ref(false)
const selectedUser = ref(null)
const imageErrors = ref({})
const overrideForm = ref({
    capability_code: '',
    effect: 'allow',
    reason: '',
    expires_at: '',
})

const createButtonText = computed(() => {
    if (props.pageKey === 'employees') {
        return canApproveRequests.value ? 'Nhân sự' : 'Tạo nhân sự'
    }

    return canApproveRequests.value ? 'Tài khoản' : 'Tạo tài khoản'
})

const createButtonTitle = computed(() => {
    if (props.pageKey === 'employees') {
        return canApproveRequests.value ? 'Thêm nhân sự mới' : 'Tạo nhân sự'
    }

    return canApproveRequests.value ? 'Thêm tài khoản mới' : 'Tạo tài khoản'
})

const detailModalClasses = [
    'relative', 'w-full', 'max-w-[900px]',
    'flex', 'flex-col', 'rounded-xl',
    'bg-white', 'dark:bg-gray-900',
    'overflow-hidden', 'max-h-[90vh]',
    'shadow-2xl'
]

const columns = computed(() => [
    { label: props.pageKey === 'employees' ? 'Nhân sự' : 'Tài khoản', key: 'user', width: '240px' },
    { label: 'Liên hệ', key: 'contact', width: '240px' },
    { label: 'Phòng ban', key: 'department' },
    { label: 'Công việc', key: 'employment', width: '200px' },
    { label: 'Trạng thái', key: 'status', width: '260px' }
])

const actions = [
    {
        icon: EyeOn,
        buttonProps: { class: 'mr-3 devc__admin__action-btn devc-btn-view', title: 'Xem chi tiết tài khoản' },
        onClick: (item) => openDetailModal(item)
    },
    {
        icon: EditButtonIcon,
        buttonProps: { class: 'devc__admin__action-btn devc-btn-edit', title: 'Chỉnh sửa' },
        onClick: (item) => openEditUserModal(item),
        hidden: (item) => !permissions.value['users.edit'] || !canManageUser(item)
    }
]

const filterConfig = computed(() => [
    { label: pageKeyLabel.value, name: 'search', type: 'text', placeholder: 'Tên, email, số điện thoại...', value: props.filters?.search ?? '' },
    {
        label: 'Phòng ban',
        name: 'department_id',
        type: 'select',
        options: [
            { value: '', label: 'Tất cả phòng ban' },
            ...props.departments.map((item) => ({ value: item.id, label: item.name }))
        ],
        value: props.filters?.department_id ?? ''
    },
    { label: 'Ngày vào làm', name: 'hire_date', type: 'date', placeholder: 'Chọn ngày vào làm', value: props.filters?.hire_date ?? '' },
    {
        label: 'Trạng thái',
        name: 'status',
        type: 'select',
        options: [
            { value: '', label: 'Tất cả trạng thái' },
            { value: 'active', label: 'Đang hoạt động' },
            { value: 'inactive', label: 'Ngừng hoạt động' },
            { value: 'pending', label: 'Đang chờ' },
            { value: 'blocked', label: 'Đã khóa' }
        ],
        value: props.filters?.status ?? ''
    }
])

const pageKeyLabel = computed(() => props.pageKey === 'employees' ? 'Tìm kiếm nhân sự' : 'Tìm kiếm tài khoản')

const breadcrumbItems = computed(() => [
    { text: 'HCNS', link: null },
    { text: props.pageKey === 'employees' ? 'Nhân sự' : 'Tài khoản', link: null },
])

const getInitials = (name = '') => {
    const parts = name.trim().split(' ')
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase()
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

const getStatusText = (status) => ({
    active: 'Hoạt động',
    inactive: 'Không hoạt động',
    pending: 'Đang chờ',
    blocked: 'Đã khóa'
}[status] || status)

const getEmploymentStatusText = (status) => ({
    active: 'Đang làm việc',
    inactive: 'Tạm ngừng',
    terminated: 'Nghỉ việc'
}[status] || '-')

const getEmploymentTypeText = (type) => ({
    probation: 'Thử việc',
    official: 'Chính thức',
    intern: 'Thực tập',
    collaborator: 'Cộng tác viên'
}[type] || '-')

const getPositionSummary = (user) => {
    if (user?.position?.name) {
        const authority = Number(user?.position?.authority_level || 0)
        return authority > 0 ? `${user.position.name} (Rank ${authority})` : user.position.name
    }

    const authority = Number(user?.position?.authority_level || user?.authority_level || 0)
    return authority > 0 ? `Rank ${authority}` : '-'
}

const getCapabilityLabel = (capability) => ({
    manage_employees: 'Quản lý nhân sự',
    view_salary: 'Xem lương',
    manage_salary: 'Quản lý lương',
    approve_attendance: 'Duyệt chấm công',
    view_all_attendance: 'Xem toàn bộ chấm công',
    export_attendance: 'Xuất chấm công',
    approve_leave: 'Duyệt nghỉ phép',
    manage_leave_policy: 'Quản lý chính sách nghỉ',
    manage_projects: 'Quản lý dự án',
    manage_project_members: 'Quản lý thành viên dự án',
    manage_project_roles: 'Quản lý vai trò dự án',
    view_all_projects: 'Xem tất cả dự án',
    manage_departments: 'Quản lý phòng ban',
    manage_positions: 'Quản lý chức vụ',
    transfer_employee: 'Điều chuyển nhân sự',
    approve_requests: 'Duyệt yêu cầu',
    sign_documents: 'Ký tài liệu',
    view_reports: 'Xem báo cáo',
    export_reports: 'Xuất báo cáo',
    view_activity_logs: 'Xem nhật ký hệ thống',
}[capability] || capability)

const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '-'
    return `${new Intl.NumberFormat('vi-VN').format(Number(value))} VND`
}

const formatDate = (value) => {
    if (!value) return '-'
    return new Date(value).toLocaleDateString('vi-VN')
}

const stripAdministrativePrefix = (name = '') => String(name || '')
    .replace(/^(thành phố|thị trấn|thị xã|tỉnh|phường|xã|thanh pho|thi tran|thi xa|tinh|phuong|xa|tp\.?|tt\.?|p\.)\s*/i, '')
    .replace(/\s+/g, ' ')
    .trim()

const formatFullAddress = (user) => {
    if (!user) return '-'

    const parts = [
        user.address_line,
        stripAdministrativePrefix(user.ward?.name),
        stripAdministrativePrefix(user.province?.name),
        user.address,
    ].filter(Boolean)

    return parts.length ? parts.join(', ') : '-'
}

const canManageUser = (user) => {
    if (!permissions.value['users.edit']) return false
    const targetAuthorityLevel = Number(user?.position?.authority_level || 0)
    if (currentAuthorityLevel.value > 0 && targetAuthorityLevel > 0 && targetAuthorityLevel >= currentAuthorityLevel.value) return false
    return true
}

const canManageOverrides = computed(() =>
    !!selectedUser.value
    && canManageUser(selectedUser.value)
    && !!permissions.value['users.edit']
)

const capabilityOptionItems = computed(() =>
    (props.capabilityOptions || []).map((item) => ({
        value: item.code,
        label: `${item.name || getCapabilityLabel(item.code)} (${item.code})`,
    }))
)

const effectOptionItems = [
    { value: 'allow', label: 'Cho phep' },
    { value: 'deny', label: 'Tu choi' },
]

const refreshDetailUser = (userId) => {
    const query = {
        ...props.filters,
        detail_user: userId,
        page: props.users?.current_page ?? 1,
        per_page: props.users?.per_page ?? props.filters?.per_page ?? 15,
    }

    router.get(currentRouteName.value, query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetOverrideForm = () => {
    overrideForm.value = {
        capability_code: '',
        effect: 'allow',
        reason: '',
        expires_at: '',
    }
}

const submitOverride = () => {
    if (!selectedUser.value?.id || !overrideForm.value.capability_code) {
        toast.error('Vui lòng chọn quyền cần ghi đè.')
        return
    }

    router.post(
        route('web.users.capability-overrides.upsert', { user: selectedUser.value.id }),
        {
            capability_code: overrideForm.value.capability_code,
            effect: overrideForm.value.effect,
            reason: overrideForm.value.reason || null,
            expires_at: overrideForm.value.expires_at || null,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Đã lưu ghi đè quyền.')
                resetOverrideForm()
                refreshDetailUser(selectedUser.value.id)
            },
            onError: () => toast.error('Không thể lưu ghi đè quyền.'),
        }
    )
}

const removeOverride = (override) => {
    if (!selectedUser.value?.id || !override?.id) return

    router.delete(
        route('web.users.capability-overrides.destroy', { user: selectedUser.value.id, override: override.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Đã xóa ghi đè quyền.')
                refreshDetailUser(selectedUser.value.id)
            },
            onError: () => toast.error('Không thể xóa ghi đè quyền.'),
        }
    )
}

const changeAccountStatus = (user, status) => {
    if (!permissions.value['users.manage_status'] || !canManageUser(user)) return

    router.put(route('web.users.account-status', user.id), { status }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Cập nhật trạng thái tài khoản thành công!'),
        onError: () => toast.error('Không thể cập nhật trạng thái tài khoản!')
    })
}

const changeEmploymentStatus = (user, employmentStatus) => {
    if (!permissions.value['users.manage_status'] || !canManageUser(user)) return

    router.put(route('web.users.employment-status', user.id), { employment_status: employmentStatus }, {
        preserveScroll: true,
        onSuccess: () => {
            if (employmentStatus === 'terminated') {
                toast.success('Đã chuyển sang nghỉ việc và khóa tài khoản.')
                return
            }

            toast.success('Cập nhật trạng thái làm việc thành công!')
        },
        onError: () => toast.error('Không thể cập nhật trạng thái làm việc!')
    })
}

const avatarUrl = (filename) => {
    if (!filename) return ''
    if (filename.startsWith('http') || filename.startsWith('/storage/')) return filename
    return `/storage/${filename}`
}

const handleImageError = (e) => {
    // Không cần ẩn trực tiếp DOM nữa vì đã dùng reactive imageErrors
}

const applyFilter = (filters) => {
    router.get(currentRouteName.value, { ...filters }, { preserveState: true, preserveScroll: true })
}

const handlePageChange = (pageNumber) => {
    router.get(currentRouteName.value, { ...props.filters, page: pageNumber }, { preserveState: true, preserveScroll: true })
}

const handleItemsPerPageChange = (perPage) => {
    router.get(currentRouteName.value, { ...props.filters, per_page: perPage, page: 1 }, { preserveState: true, preserveScroll: true })
}

const currentRouteName = computed(() =>
    props.pageKey === 'employees'
        ? route('web.users.employees')
        : route('web.users.index')
)

const openAddUserModal = () => {
    isUserEditMode.value = false
    selectedUser.value = null
    isUserModalOpen.value = true
}

const openEditUserModal = async (user) => {
    if (!canManageUser(user)) return

    isUserEditMode.value = true
    selectedUser.value = null
    await nextTick()
    selectedUser.value = user
    isUserModalOpen.value = true
}

const openDetailModal = (user) => {
    selectedUser.value = user
    resetOverrideForm()
    isDetailModalOpen.value = true
}

const goToApprovals = () => {
    router.get(route('web.user-approvals.index'))
}

const goToRequests = () => {
    router.get(route('web.users.employee-requests'))
}

const closeDetailModal = () => {
    isDetailModalOpen.value = false
    selectedUser.value = null
}

const handleModalSuccess = () => {}

watch(() => props.detailUser, (detailUser) => {
    if (!detailUser) return
    openDetailModal(detailUser)
}, { immediate: true })
</script>




