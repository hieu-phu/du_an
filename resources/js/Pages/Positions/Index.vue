<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Modal from '@/components/ui/Modal.vue'
const props = defineProps({
    positions: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    capabilityOptions: { type: Array, default: () => [] },
    authorityLevels: { type: Array, default: () => [] },
    authorityLevelCatalog: { type: Array, default: () => [] },
})
const page = usePage()
const roles = computed(() => page.props.auth?.user?.roles || [])
const isAdmin = computed(() => roles.value.includes('admin'))
const filters = reactive({
    search: props.filters?.search ?? '',
    status: props.filters?.status ?? '',
})
const applyFilter = () => {
    router.get(
        route('positions.index'),
        {
            search: filters.search || undefined,
            status: filters.status || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true }
    )
}
const resetFilter = () => {
    filters.search = ''
    filters.status = ''
    applyFilter()
}
const authorityOptions = computed(() => {
    if (Array.isArray(props.authorityLevels) && props.authorityLevels.length > 0) {
        return props.authorityLevels.map((item) => ({
            value: Number(item.value),
            label: String(item.label || `Mức ${item.value}`),
        }))
    }

    return [
        { value: 1, label: 'Mức 1 - Nhân viên' },
        { value: 2, label: 'Mức 2 - Tổ phó / Senior' },
        { value: 3, label: 'Mức 3 - Trưởng nhóm' },
        { value: 4, label: 'Mức 4 - Trưởng phòng' },
        { value: 5, label: 'Mức 5 - Giám đốc / Quản lý cao' },
    ]
})
const authorityLabelMap = computed(() =>
    Object.fromEntries(authorityOptions.value.map((o) => [o.value, o.label]))
)
const capabilityGroups = computed(() => {
    const baseGroups = (page.props.auth?.capability_definitions || []).map((group) => ({
        ...group,
        items: [...(group.items || [])],
    }))

    const existingKeys = new Set(
        baseGroups.flatMap((group) => (group.items || []).map((item) => item.key))
    )

    const extras = (props.capabilityOptions || []).filter((item) => !existingKeys.has(item.key))
    if (!extras.length) {
        return baseGroups
    }

    const moduleLabel = (module) => {
        const normalized = String(module || 'custom').replace(/_/g, ' ')
        return normalized.charAt(0).toUpperCase() + normalized.slice(1)
    }

    const grouped = new Map()
    for (const item of extras) {
        const groupName = `Mở rộng: ${moduleLabel(item.module)}`
        if (!grouped.has(groupName)) {
            grouped.set(groupName, [])
        }
        grouped.get(groupName).push({
            key: item.key,
            label: item.label || item.key,
            desc: item.desc || 'Quyền tùy chỉnh do Admin thêm.',
        })
    }

    for (const [groupName, items] of grouped.entries()) {
        baseGroups.push({
            group: groupName,
            icon: '🧩',
            items,
        })
    }

    return baseGroups
})
const allCapabilities = computed(() =>
    capabilityGroups.value.flatMap((g) => g.items)
)
const capabilityLabelMap = computed(() =>
    Object.fromEntries(allCapabilities.value.map((c) => [c.key, c.label]))
)
const capabilityMinAuthority = {
    manage_positions: 5,
    view_activity_logs: 5,
    sign_documents: 5,
    manage_employees: 4,
    manage_salary: 4,
    view_salary: 4,
    manage_departments: 4,
    transfer_employee: 4,
    approve_attendance: 4,
    approve_leave: 4,
    approve_requests: 4,
}
const impliedCapabilities = {
    manage_salary: ['view_salary'],
    export_attendance: ['view_all_attendance'],
    manage_project_roles: ['manage_project_members', 'manage_projects'],
    export_reports: ['view_reports'],
}
const isFormModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const isAuthorityLevelModalOpen = ref(false)
const isEditing = ref(false)
const selectedPosition = ref(null)
const editingId = ref(null)
const activeTab = ref('info')
const form = useForm({
    name: '',
    description: '',
    authority_level: '',
    capabilities: [],
    is_active: true,
})
const capabilityCreateForm = useForm({
    name: '',
    module: 'custom',
    description: '',
})
const authorityLevelForm = useForm({
    rank: '',
    name: '',
    is_active: true,
})
const showCapabilityCreator = ref(false)
const emptyMessage = computed(() =>
    filters.search || filters.status
        ? 'Không tìm thấy chức vụ phù hợp.'
        : 'Chưa có dữ liệu chức vụ.'
)
const capabilityRequiredAuthority = (key) => Number(capabilityMinAuthority[key] || 1)
const hasCapability = (capabilities, key) =>
    Array.isArray(capabilities) && capabilities.includes(key)
const roleFromAuthorityLevel = (authorityLevel) => {
    const level = Number(authorityLevel || 0)
    if (level >= 5) return 'admin'
    if (level >= 4) return 'hr'
    return 'employee'
}
const roleRank = (role) => ({ employee: 1, hr: 2, admin: 3 }[role] || 1)
const minimumRoleByCapabilities = computed(() => {
    const maxRequiredLevel = form.capabilities.reduce((carry, key) => {
        return Math.max(carry, capabilityRequiredAuthority(key))
    }, 1)
    return roleFromAuthorityLevel(maxRequiredLevel)
})
const minimumRoleByAuthority = computed(() =>
    roleFromAuthorityLevel(Number(form.authority_level || 0))
)
const finalMinimumRole = computed(() => {
    return roleRank(minimumRoleByCapabilities.value) > roleRank(minimumRoleByAuthority.value)
        ? minimumRoleByCapabilities.value
        : minimumRoleByAuthority.value
})
const finalMinimumRoleLabel = computed(() => ({
    employee: 'Nhân viên',
    hr: 'HR',
    admin: 'Admin',
}[finalMinimumRole.value] || 'Nhân viên'))
const ensureAuthorityForCapability = (key) => {
    const required = capabilityRequiredAuthority(key)
    const current = Number(form.authority_level || 0)
    if (current < required) {
        form.authority_level = required
    }
}
const addCapability = (key) => {
    if (!form.capabilities.includes(key)) {
        form.capabilities.push(key)
    }
}
const applyImpliedCapabilities = () => {
    let changed = false
    do {
        changed = false
        for (const key of [...form.capabilities]) {
            for (const impliedKey of impliedCapabilities[key] || []) {
                ensureAuthorityForCapability(impliedKey)
                if (!form.capabilities.includes(impliedKey)) {
                    form.capabilities.push(impliedKey)
                    changed = true
                }
            }
        }
    } while (changed)
}
const removeCapabilitiesAboveAuthority = (authorityLevel) => {
    const level = Number(authorityLevel || 0)
    const before = form.capabilities.length
    form.capabilities = form.capabilities.filter((key) => capabilityRequiredAuthority(key) <= level)
    const removed = before - form.capabilities.length
    if (removed > 0) {
        toast.info(`Đã bỏ ${removed} quyền không phù hợp với mức quyền hạn.`)
    }
}
const toggleCapability = (key) => {
    const idx = form.capabilities.indexOf(key)
    if (idx === -1) {
        ensureAuthorityForCapability(key)
        addCapability(key)
        applyImpliedCapabilities()
    } else {
        form.capabilities.splice(idx, 1)
    }
}
const selectAllInGroup = (group) => {
    group.items.forEach(({ key }) => {
        ensureAuthorityForCapability(key)
        addCapability(key)
    })
    applyImpliedCapabilities()
}
const clearAllInGroup = (group) => {
    group.items.forEach(({ key }) => {
        const idx = form.capabilities.indexOf(key)
        if (idx !== -1) form.capabilities.splice(idx, 1)
    })
}
const isGroupFullySelected = (group) =>
    group.items.every(({ key }) => form.capabilities.includes(key))
const openCreateModal = () => {
    isEditing.value = false
    editingId.value = null
    activeTab.value = 'info'
    form.reset()
    form.clearErrors()
    form.is_active = true
    form.authority_level = ''
    form.capabilities = []
    showCapabilityCreator.value = false
    capabilityCreateForm.reset()
    capabilityCreateForm.clearErrors()
    capabilityCreateForm.module = 'custom'
    isFormModalOpen.value = true
}
const openEditModal = (position) => {
    isEditing.value = true
    editingId.value = position.id
    activeTab.value = 'info'
    form.clearErrors()
    form.name = position.name ?? ''
    form.description = position.description ?? ''
    form.authority_level = position.authority_level ?? ''
    form.capabilities = Array.isArray(position.capabilities) ? [...position.capabilities] : []
    form.is_active = !!position.is_active
    applyImpliedCapabilities()
    showCapabilityCreator.value = false
    capabilityCreateForm.reset()
    capabilityCreateForm.clearErrors()
    capabilityCreateForm.module = 'custom'
    isFormModalOpen.value = true
}
const openDetailModal = (position) => {
    selectedPosition.value = position
    isDetailModalOpen.value = true
}
const closeFormModal = () => {
    isFormModalOpen.value = false
    form.clearErrors()
    capabilityCreateForm.clearErrors()
    showCapabilityCreator.value = false
}
const submit = () => {
    applyImpliedCapabilities()
    const payload = {
        ...form.data(),
        authority_level: form.authority_level === '' ? null : Number(form.authority_level),
    }
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            isFormModalOpen.value = false
            form.reset()
            toast.success(isEditing.value ? 'Đã cập nhật chức vụ.' : 'Đã tạo chức vụ mới.')
        },
        onError: (errors) => {
            if (errors.name || errors.authority_level || errors.description) {
                activeTab.value = 'info'
            }
            if (errors.capabilities || Object.keys(errors).some((key) => key.startsWith('capabilities.'))) {
                activeTab.value = 'capabilities'
            }
            toast.error('Vui lòng sửa các trường đang báo lỗi trong form.')
        },
    }
    if (isEditing.value) {
        return form.transform(() => payload).put(route('positions.update', editingId.value), options)
    }
    return form.transform(() => payload).post(route('positions.store'), options)
}
const submitNewCapability = () => {
    capabilityCreateForm.post(route('positions.capabilities.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Đã thêm quyền mới.')
            capabilityCreateForm.reset()
            capabilityCreateForm.module = 'custom'
            showCapabilityCreator.value = false
        },
        onError: () => {
            toast.error('Không thể thêm quyền. Vui lòng kiểm tra lại.')
        },
    })
}
const openAuthorityLevelModal = () => {
    authorityLevelForm.reset()
    authorityLevelForm.clearErrors()
    authorityLevelForm.is_active = true
    isAuthorityLevelModalOpen.value = true
}
const closeAuthorityLevelModal = () => {
    isAuthorityLevelModalOpen.value = false
    authorityLevelForm.clearErrors()
}
const submitAuthorityLevel = () => {
    const payload = {
        rank: authorityLevelForm.rank === '' ? null : Number(authorityLevelForm.rank),
        name: authorityLevelForm.name,
        is_active: !!authorityLevelForm.is_active,
    }

    authorityLevelForm.transform(() => payload).post(route('positions.authority-levels.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Đã thêm mức quyền hạn.')
            authorityLevelForm.reset()
            authorityLevelForm.is_active = true
        },
        onError: () => {
            toast.error('Không thể thêm mức quyền hạn.')
        },
    })
}
const toggleAuthorityLevel = (level) => {
    const nextAction = level.is_active ? 'khóa' : 'mở'
    if (!window.confirm(`Bạn có chắc muốn ${nextAction} mức "${level.name}"?`)) return
    router.put(route('positions.authority-levels.toggle', level.id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success(`Đã ${nextAction} mức quyền hạn.`),
        onError: () => toast.error(`Không thể ${nextAction} mức quyền hạn.`),
    })
}
watch(() => form.authority_level, (nextLevel, prevLevel) => {
    const next = Number(nextLevel || 0)
    const prev = Number(prevLevel || 0)
    if (next <= 0) {
        return
    }
    if (prev > 0 && next < prev) {
        removeCapabilitiesAboveAuthority(next)
    }
})
watch(() => form.capabilities, () => {
    applyImpliedCapabilities()
}, { deep: true })
const toggleStatus = (position) => {
    const nextAction = position.is_active ? 'khóa' : 'mở lại'
    if (!window.confirm(`Bạn có chắc muốn ${nextAction} chức vụ "${position.name}"?`)) return
    router.put(route('positions.toggle', position.id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success(`Đã ${nextAction} chức vụ.`),
        onError: () => toast.error(`Không thể ${nextAction} chức vụ.`),
    })
}
const confirmDelete = (position) => {
    if (position.employee_profiles_count > 0) {
        window.alert(`Chức vụ "${position.name}" đang có ${position.employee_profiles_count} nhân viên. Không thể xóa.`)
        return
    }
    if (!window.confirm(`Bạn có chắc muốn xóa chức vụ "${position.name}"?`)) return
    router.delete(route('positions.destroy', position.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Đã xóa chức vụ.'),
        onError: () => toast.error('Không thể xóa chức vụ.'),
    })
}
</script>

<template>
    <Head title="Quản lý Chức vụ" />

    <AdminLayout>
        <PageBreadcrumb
            title="Danh sách chức vụ"
            :items="[{ text: 'HCNS', link: null }, { text: 'Chức vụ', link: null }]"
        />

        <!-- Filter Bar -->
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Tìm kiếm chức vụ</label>
                        <input
                            v-model="filters.search"
                            type="text"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                            placeholder="Nhập tên chức vụ..."
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
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="resetFilter"
                    >Xóa lọc</button>
                    <button
                        type="button"
                        class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                        @click="applyFilter"
                    >Tìm kiếm</button>
                    <button
                        v-if="isAdmin"
                        type="button"
                        class="rounded-xl border border-indigo-300 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100"
                        @click="openAuthorityLevelModal"
                    >Mức quyền hạn</button>
                    <button
                        type="button"
                        class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        @click="openCreateModal"
                    >+ Thêm chức vụ</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tên chức vụ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Mức quyền hạn</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Mô tả</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Quyền hạn</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Số nhân sự</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Trạng thái</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="position in positions" :key="position.id" class="align-top">
                            <td class="px-4 py-4 text-sm">
                                <div class="font-semibold text-gray-900">{{ position.name }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    {{ authorityLabelMap[position.authority_level] ?? 'Chưa thiết lập' }}
                                </span>
                            </td>
                            <td class="max-w-[200px] px-4 py-4 text-sm text-gray-500">
                                {{ position.description || '—' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span
                                    v-if="position.capabilities?.length"
                                    class="inline-flex cursor-pointer rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-200"
                                    @click="openDetailModal(position)"
                                >
                                    {{ position.capabilities.length }} quyền
                                </span>
                                <span v-else class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-400">
                                    Chưa cấp
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span
                                    :class="position.employee_profiles_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400'"
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                >
                                    {{ position.employee_profiles_count > 0 ? `${position.employee_profiles_count} nhân viên` : 'Chưa sử dụng' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span
                                    :class="position.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                >
                                    {{ position.is_active ? 'Đang hoạt động' : 'Tạm khóa' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                        @click="openDetailModal(position)"
                                    >Xem</button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
                                        @click="openEditModal(position)"
                                    >Sửa</button>
                                    <button
                                        type="button"
                                        class="rounded-lg px-3 py-2 text-sm font-medium text-white transition"
                                        :class="position.is_active ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700'"
                                        @click="toggleStatus(position)"
                                    >{{ position.is_active ? 'Khóa' : 'Mở' }}</button>
                                    <button
                                        v-if="isAdmin && position.employee_profiles_count === 0"
                                        type="button"
                                        class="rounded-lg bg-rose-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-rose-600"
                                        @click="confirmDelete(position)"
                                    >Xóa</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!positions.length">
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                                {{ emptyMessage }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Modal (Create / Edit) -->
        <Modal :show="isFormModalOpen" @close="closeFormModal" max-width="2xl">
            <div class="max-h-[85vh] overflow-y-auto p-6">
                <h2 class="mb-5 text-lg font-semibold text-gray-900">
                    {{ isEditing ? 'Chỉnh sửa chức vụ' : 'Tạo chức vụ mới' }}
                </h2>

                <!-- Tabs -->
                <div class="mb-5 flex gap-1 rounded-xl bg-gray-100 p-1">
                    <button
                        type="button"
                        class="flex-1 rounded-lg py-2 text-sm font-semibold transition"
                        :class="activeTab === 'info' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        @click="activeTab = 'info'"
                    >
                        Thông tin cơ bản
                    </button>
                    <button
                        type="button"
                        class="flex-1 rounded-lg py-2 text-sm font-semibold transition"
                        :class="activeTab === 'capabilities' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        @click="activeTab = 'capabilities'"
                    >
                        Quyền hạn
                        <span
                            v-if="form.capabilities.length"
                            class="ml-1.5 inline-flex items-center justify-center rounded-full bg-blue-500 px-1.5 py-0.5 text-[10px] font-bold text-white"
                        >{{ form.capabilities.length }}</span>
                    </button>
                </div>

                <form @submit.prevent="submit">
                    <!-- Tab: Thông tin cơ bản -->
                    <div v-show="activeTab === 'info'" class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Tên chức vụ <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                                placeholder="Nhập tên chức vụ"
                            />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-rose-600">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Mức quyền hạn</label>
                            <select
                                v-model="form.authority_level"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                            >
                                <option value="">— Chưa chọn mức quyền hạn —</option>
                                <option v-for="option in authorityOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                            <div v-if="form.errors.authority_level" class="mt-1 text-sm text-rose-600">{{ form.errors.authority_level }}</div>
                            <div class="mt-1 text-xs text-gray-500">
                                Role tối thiểu suy ra từ mức + quyền hạn: <span class="font-semibold text-gray-700">{{ finalMinimumRoleLabel }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Mô tả</label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500"
                                placeholder="Nhập mô tả chức vụ..."
                            />
                        </div>

                        <label class="flex items-center gap-3 text-sm text-gray-700">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            Trạng thái hoạt động
                        </label>

                        <div class="flex justify-end pt-2">
                            <button
                                type="button"
                                class="rounded-xl bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                                @click="activeTab = 'capabilities'"
                            >
                                Tiếp theo: Phân quyền hạn →
                            </button>
                        </div>
                    </div>

                    <!-- Tab: Quyền hạn -->
                    <div v-show="activeTab === 'capabilities'" class="space-y-4">
                        <p class="text-sm text-gray-500">
                            Chọn các quyền hạn nghiệp vụ mà nhân viên giữ chức vụ này được phép thực hiện.
                        </p>

                        <div v-if="isAdmin" class="rounded-xl border border-indigo-200 bg-indigo-50/60 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-indigo-700">Quản lý quyền tùy chỉnh</div>
                                <button
                                    type="button"
                                    class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700"
                                    @click="showCapabilityCreator = !showCapabilityCreator"
                                >
                                    {{ showCapabilityCreator ? 'Ẩn form thêm quyền' : '+ Thêm quyền mới' }}
                                </button>
                            </div>

                            <div v-show="showCapabilityCreator" class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-700">Tên quyền</label>
                                    <input
                                        v-model="capabilityCreateForm.name"
                                        type="text"
                                        class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                                        placeholder="Ví dụ: Quản lý thiết bị"
                                    />
                                    <div v-if="capabilityCreateForm.errors.name" class="mt-1 text-xs text-rose-600">
                                        {{ capabilityCreateForm.errors.name }}
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-700">Nhóm module</label>
                                    <input
                                        v-model="capabilityCreateForm.module"
                                        type="text"
                                        class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                                        placeholder="vd: custom"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-700">Mô tả</label>
                                    <input
                                        v-model="capabilityCreateForm.description"
                                        type="text"
                                        class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                                        placeholder="Mô tả ngắn về quyền"
                                    />
                                </div>
                                <div class="md:col-span-2 flex justify-end">
                                    <button
                                        type="button"
                                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-60"
                                        :disabled="capabilityCreateForm.processing"
                                        @click="submitNewCapability"
                                    >
                                        {{ capabilityCreateForm.processing ? 'Đang thêm...' : 'Thêm quyền mới' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="max-h-[360px] space-y-4 overflow-y-auto pr-1">
                            <div
                                v-for="group in capabilityGroups"
                                :key="group.group"
                                class="rounded-xl border border-gray-200 p-4"
                            >
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">{{ group.icon }}</span>
                                        <span class="text-sm font-semibold text-gray-800">{{ group.group }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            class="rounded-lg px-2.5 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50"
                                            @click="selectAllInGroup(group)"
                                        >Chọn tất cả</button>
                                        <button
                                            type="button"
                                            class="rounded-lg px-2.5 py-1 text-xs font-medium text-gray-500 hover:bg-gray-100"
                                            @click="clearAllInGroup(group)"
                                        >Bỏ chọn</button>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        v-for="item in group.items"
                                        :key="item.key"
                                        class="flex cursor-pointer items-start gap-3 rounded-lg p-2.5 transition hover:bg-gray-50"
                                        :class="{ 'bg-blue-50 hover:bg-blue-100': form.capabilities.includes(item.key) }"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="form.capabilities.includes(item.key)"
                                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            @change="toggleCapability(item.key)"
                                        />
                                        <div>
                                            <div class="text-sm font-medium text-gray-800">{{ item.label }}</div>
                                            <div class="text-xs text-gray-500">{{ item.desc }}</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                            Đã chọn <strong>{{ form.capabilities.length }}</strong> / {{ allCapabilities.length }} quyền hạn
                        </div>
                        <div v-if="form.errors.capabilities" class="text-sm text-rose-600">{{ form.errors.capabilities }}</div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-5 flex justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            @click="closeFormModal"
                        >Hủy</button>
                        <button
                            type="submit"
                            class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Đang lưu...' : (isEditing ? 'Cập nhật' : 'Tạo mới') }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Authority Level Modal -->
        <Modal :show="isAuthorityLevelModalOpen" @close="closeAuthorityLevelModal" max-width="2xl">
            <div class="p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Quản lý mức quyền hạn</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Admin có thể thêm và khóa/mở các mức quyền hạn.</p>
                    </div>
                </div>

                <form class="rounded-xl border border-indigo-200 bg-indigo-50/60 p-4" @submit.prevent="submitAuthorityLevel">
                    <div class="mb-3 text-sm font-semibold text-indigo-700">Thêm mức mới</div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700">Thứ bậc (rank)</label>
                            <input
                                v-model="authorityLevelForm.rank"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                                placeholder="Ví dụ: 6"
                            />
                            <div v-if="authorityLevelForm.errors.rank" class="mt-1 text-xs text-rose-600">{{ authorityLevelForm.errors.rank }}</div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-700">Tên mức</label>
                            <input
                                v-model="authorityLevelForm.name"
                                type="text"
                                class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:border-indigo-500"
                                placeholder="Ví dụ: Mức 6 - Phó tổng"
                            />
                            <div v-if="authorityLevelForm.errors.name" class="mt-1 text-xs text-rose-600">{{ authorityLevelForm.errors.name }}</div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input v-model="authorityLevelForm.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            Kích hoạt ngay
                        </label>
                        <button
                            type="submit"
                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-60"
                            :disabled="authorityLevelForm.processing"
                        >
                            {{ authorityLevelForm.processing ? 'Đang thêm...' : 'Thêm mức' }}
                        </button>
                    </div>
                </form>

                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Rank</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Tên mức</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Trạng thái</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="level in authorityLevelCatalog" :key="level.id">
                                <td class="px-3 py-2 text-sm font-semibold text-gray-800">{{ level.rank }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ level.name }}</td>
                                <td class="px-3 py-2 text-center">
                                    <span
                                        :class="level.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                    >
                                        {{ level.is_active ? 'Đang bật' : 'Đang khóa' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50"
                                        @click="toggleAuthorityLevel(level)"
                                    >
                                        {{ level.is_active ? 'Khóa' : 'Mở' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!authorityLevelCatalog.length">
                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">Chưa có mức quyền hạn.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 flex justify-end">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="closeAuthorityLevelModal"
                    >Đóng</button>
                </div>
            </div>
        </Modal>

        <!-- Detail Modal -->
        <Modal :show="isDetailModalOpen" @close="isDetailModalOpen = false" max-width="2xl">
            <div v-if="selectedPosition" class="p-6">
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ selectedPosition.name }}</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Chi tiết chức vụ</p>
                    </div>
                    <span
                        :class="selectedPosition.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                        class="rounded-full px-3 py-1 text-xs font-semibold"
                    >
                        {{ selectedPosition.is_active ? 'Đang hoạt động' : 'Tạm khóa' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Thông tin chung -->
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Thông tin chung</div>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div>
                                <span class="font-medium text-gray-500">Mức quyền hạn:</span>
                                <span class="ml-2 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                    {{ authorityLabelMap[selectedPosition.authority_level] ?? 'Chưa thiết lập' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Số nhân viên:</span>
                                <span :class="selectedPosition.employee_profiles_count > 0 ? 'text-blue-600 font-bold' : 'text-gray-400'" class="ml-2">
                                    {{ selectedPosition.employee_profiles_count }} người
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Mô tả:</span>
                                <p class="mt-1 text-gray-600">{{ selectedPosition.description || 'Chưa có mô tả.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tổng quan quyền hạn -->
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="mb-3 text-sm font-semibold text-gray-900">Tổng quan quyền hạn</div>
                        <div v-if="selectedPosition.capabilities?.length" class="space-y-1">
                            <div class="mb-2 text-xs text-gray-500">
                                Đã cấp <strong class="text-blue-600">{{ selectedPosition.capabilities.length }}</strong> / {{ allCapabilities.length }} quyền
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="cap in selectedPosition.capabilities.slice(0, 6)"
                                    :key="cap"
                                    class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700"
                                >
                                    {{ capabilityLabelMap[cap] ?? cap }}
                                </span>
                                <span
                                    v-if="selectedPosition.capabilities.length > 6"
                                    class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500"
                                >
                                    +{{ selectedPosition.capabilities.length - 6 }} quyền khác
                                </span>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-400">Chức vụ này chưa được cấp quyền hạn nào.</div>
                    </div>
                </div>

                <!-- Chi tiết quyền hạn theo nhóm -->
                <div v-if="selectedPosition.capabilities?.length" class="mt-4 rounded-xl border border-gray-200 p-4">
                    <div class="mb-3 text-sm font-semibold text-gray-900">Chi tiết quyền hạn theo nhóm</div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="group in capabilityGroups"
                            :key="group.group"
                            class="rounded-lg bg-gray-50 p-3"
                        >
                            <div class="mb-2 flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                                <span>{{ group.icon }}</span>
                                <span>{{ group.group }}</span>
                            </div>
                            <div class="space-y-1">
                                <div
                                    v-for="item in group.items"
                                    :key="item.key"
                                    class="flex items-center gap-2 text-xs"
                                >
                                    <span
                                        :class="hasCapability(selectedPosition.capabilities, item.key)
                                            ? 'text-emerald-500'
                                            : 'text-gray-300'"
                                    >
                                        {{ hasCapability(selectedPosition.capabilities, item.key) ? '✓' : '✗' }}
                                    </span>
                                    <span :class="hasCapability(selectedPosition.capabilities, item.key) ? 'text-gray-700' : 'text-gray-400'">
                                        {{ item.label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-blue-200 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-50"
                        @click="() => { isDetailModalOpen = false; openEditModal(selectedPosition) }"
                    >Chỉnh sửa</button>
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="isDetailModalOpen = false"
                    >Đóng</button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
