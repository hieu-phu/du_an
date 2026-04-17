<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Modal from '@/components/ui/Modal.vue'

const props = defineProps({
    departments: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const page = usePage()
const permissions = computed(() => page.props.auth?.permissions || {})
const canApproveRequests = computed(() => page.props.auth?.position_capabilities?.approve_requests === true)

const filters = reactive({
    search: props.filters?.search ?? '',
    status: props.filters?.status ?? '',
})

const isFormModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const isEditing = ref(false)
const selectedDepartment = ref(null)
const editingId = ref(null)

const form = useForm({
    name: '',
    description: '',
    manager_user_id: '',
    is_active: true,
})

const managerOptions = computed(() => props.users.map((user) => ({
    value: user.id,
    label: user.name,
})))

const pageTitle = 'Danh sách phòng ban'

const submitLabel = computed(() => {
    if (canApproveRequests.value) {
        return isEditing.value ? 'Cập nhật phòng ban' : 'Thêm phòng ban'
    }

    return isEditing.value ? 'Gửi duyệt cập nhật' : 'Gửi duyệt phòng ban'
})

const emptyMessage = computed(() => filters.search || filters.status
    ? 'Không tìm thấy phòng ban phù hợp.'
    : 'Chưa có dữ liệu phòng ban.')

const statusText = (isActive) => isActive ? 'Đang hoạt động' : 'Tạm khóa'

const requestTypeText = computed(() => canApproveRequests.value
    ? null
    : 'Các thao tác thêm, sửa, khóa hoặc mở phòng ban của HR sẽ được gửi Admin duyệt trước khi áp dụng.')

const applyFilter = () => {
    router.get(route('departments.index'), {
        search: filters.search || undefined,
        status: filters.status || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const resetFilter = () => {
    filters.search = ''
    filters.status = ''
    applyFilter()
}

const openCreateModal = () => {
    isEditing.value = false
    editingId.value = null
    form.reset()
    form.clearErrors()
    form.is_active = true
    isFormModalOpen.value = true
}

const openEditModal = (department) => {
    isEditing.value = true
    editingId.value = department.id
    form.clearErrors()
    form.name = department.name ?? ''
    form.description = department.description ?? ''
    form.manager_user_id = department.manager_user_id ?? ''
    form.is_active = !!department.is_active
    isFormModalOpen.value = true
}

const openDetailModal = (department) => {
    selectedDepartment.value = department
    isDetailModalOpen.value = true
}

const closeFormModal = () => {
    isFormModalOpen.value = false
    form.clearErrors()
}

const submit = () => {
    if (isEditing.value) {
        return form.put(route('departments.update', editingId.value), submitOptions())
    }

    return form.post(route('departments.store'), submitOptions())
}

const submitOptions = () => ({
    preserveScroll: true,
    onSuccess: () => {
        isFormModalOpen.value = false
        form.reset()
        toast.success(canApproveRequests.value
            ? (isEditing.value ? 'Đã cập nhật phòng ban.' : 'Đã tạo phòng ban mới.')
            : (isEditing.value ? 'Đã gửi yêu cầu cập nhật phòng ban.' : 'Đã gửi yêu cầu tạo phòng ban.')
        )
    },
    onError: () => {
        toast.error(canApproveRequests.value
            ? 'Không thể lưu phòng ban.'
            : 'Không thể gửi yêu cầu phòng ban.'
        )
    },
})

const toggleStatus = (department) => {
    const nextAction = department.is_active ? 'khóa' : 'mở lại'
    const confirmed = window.confirm(`Bạn có chắc muốn ${nextAction} phòng ban "${department.name}"?`)

    if (!confirmed) {
        return
    }

    router.put(route('departments.toggle', department.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(canApproveRequests.value
                ? `Đã ${nextAction} phòng ban.`
                : `Đã gửi yêu cầu ${nextAction} phòng ban.`
            )
        },
        onError: () => {
            toast.error(canApproveRequests.value
                ? `Không thể ${nextAction} phòng ban.`
                : `Không thể gửi yêu cầu ${nextAction} phòng ban.`
            )
        },
    })
}
</script>

<template>
    <Head :title="pageTitle" />

    <AdminLayout>
        <PageBreadcrumb
            :title="pageTitle"
            :items="[
                { text: 'HCNS', link: null },
                { text: 'Phòng ban', link: null },
            ]"
        />

        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Tìm kiếm phòng ban</label>
                        <input
                            v-model="filters.search"
                            type="text"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                            placeholder="Nhập tên phòng ban..."
                            @keyup.enter="applyFilter"
                        />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Trạng thái</label>
                        <select
                            v-model="filters.status"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                        >
                            <option value="">Tất cả trạng thái</option>
                            <option value="active">Đang hoạt động</option>
                            <option value="inactive">Tạm khóa</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link
                        v-if="permissions['departments.approvals.view']"
                        :href="route('web.department-approvals.index')"
                        class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-semibold text-violet-700 transition hover:bg-violet-100"
                    >
                        Duyệt phòng ban
                    </Link>
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="resetFilter"
                    >
                        Xóa lọc
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                        @click="applyFilter"
                    >
                        Tìm kiếm
                    </button>
                    <button
                        v-if="permissions['departments.manage']"
                        type="button"
                        class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        @click="openCreateModal"
                    >
                        {{ canApproveRequests ? 'Thêm phòng ban' : 'Gửi duyệt phòng ban' }}
                    </button>
                </div>
            </div>

            <div v-if="requestTypeText" class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ requestTypeText }}
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Phòng ban</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Trưởng phòng</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Mô tả</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Số lượng nhân sự</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Trạng thái</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="department in departments" :key="department.id" class="align-top">
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div class="font-semibold text-gray-900">{{ department.name }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div class="font-medium text-gray-900">{{ department.manager?.name || 'Chưa có' }}</div>
                                <div class="text-xs text-gray-500">{{ department.manager?.email || '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ department.description || 'Chưa cập nhật mô tả.' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {{ department.employee_profiles_count }} nhân sự
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span
                                    :class="department.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                >
                                    {{ statusText(department.is_active) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                        @click="openDetailModal(department)"
                                    >
                                        Xem
                                    </button>
                                    <button
                                        v-if="permissions['departments.manage']"
                                        type="button"
                                        class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
                                        @click="openEditModal(department)"
                                    >
                                        {{ canApproveRequests ? 'Sửa' : 'Gửi sửa' }}
                                    </button>
                                    <button
                                        v-if="permissions['departments.manage']"
                                        type="button"
                                        class="rounded-lg px-3 py-2 text-sm font-medium text-white transition"
                                        :class="department.is_active ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700'"
                                        @click="toggleStatus(department)"
                                    >
                                        {{ department.is_active ? (canApproveRequests ? 'Khóa' : 'Gửi khóa') : (canApproveRequests ? 'Mở' : 'Gửi mở') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!departments.length">
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
                                {{ emptyMessage }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Modal :show="isFormModalOpen" @close="closeFormModal">
            <div class="p-6">
                <h2 class="mb-5 text-lg font-semibold text-gray-900">
                    {{ isEditing ? 'Chỉnh sửa phòng ban' : 'Tạo phòng ban mới' }}
                </h2>

                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Tên phòng ban</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                            placeholder="Nhập tên phòng ban"
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-rose-600">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Trưởng phòng</label>
                        <select
                            v-model="form.manager_user_id"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                        >
                            <option value="">Chưa chọn trưởng phòng</option>
                            <option v-for="option in managerOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.manager_user_id" class="mt-1 text-sm text-rose-600">{{ form.errors.manager_user_id }}</div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                            placeholder="Nhập mô tả phòng ban"
                        ></textarea>
                        <div v-if="form.errors.description" class="mt-1 text-sm text-rose-600">{{ form.errors.description }}</div>
                    </div>

                    <label class="flex items-center gap-3 text-sm text-gray-700">
                        <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        Trạng thái hoạt động
                    </label>

                    <div class="flex justify-end gap-3 pt-2">
                        <button
                            type="button"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            @click="closeFormModal"
                        >
                            Hủy
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            {{ submitLabel }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="isDetailModalOpen" @close="isDetailModalOpen = false">
            <div v-if="selectedDepartment" class="p-6">
                <h2 class="mb-5 text-lg font-semibold text-gray-900">Chi tiết phòng ban</h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin chung</div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div><span class="font-medium text-gray-900">Tên phòng ban:</span> {{ selectedDepartment.name }}</div>
                            <div><span class="font-medium text-gray-900">Trạng thái:</span> {{ statusText(selectedDepartment.is_active) }}</div>
                            <div><span class="font-medium text-gray-900">Số lượng nhân sự:</span> {{ selectedDepartment.employee_profiles_count }}</div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin quản lý</div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div><span class="font-medium text-gray-900">Trưởng phòng:</span> {{ selectedDepartment.manager?.name || 'Chưa có' }}</div>
                            <div><span class="font-medium text-gray-900">Email trưởng phòng:</span> {{ selectedDepartment.manager?.email || '-' }}</div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4 md:col-span-2">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Mô tả</div>
                        <div class="text-sm text-gray-700">
                            {{ selectedDepartment.description || 'Chưa có mô tả cho phòng ban này.' }}
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

