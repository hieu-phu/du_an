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
    canCreateCustomCapabilities: { type: Boolean, default: false },
    authorityLevels: { type: Array, default: () => [] },
    authorityLevelCatalog: { type: Array, default: () => [] },
})
const page = usePage()
const positionCapabilities = computed(() => page.props.auth?.position_capabilities || {})
const canManageAuthorityLevels = computed(() => positionCapabilities.value.manage_positions === true)
const canCreateCustomCapabilities = computed(() => props.canCreateCustomCapabilities === true)
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
    if (!canCreateCustomCapabilities.value) {
        toast.error('Chức năng thêm quyền tùy chỉnh đã được tắt.')
        return
    }

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
const canEditPosition = (position) => position?.can_edit !== false
const canTogglePosition = (position) => position?.can_toggle !== false
</script>

<template>
    <Head title="Quản lý Chức vụ" />

    <AdminLayout>
        <div class="space-y-6">
            <PageBreadcrumb
                title="Quản lý chức vụ"
                :items="[{ text: 'HCNS', link: null }, { text: 'Chức vụ', link: null }]"
            />

            <!-- CONTROL PANEL -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2 lg:max-w-xl">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tìm kiếm</label>
                            <input
                                v-model="filters.search"
                                type="text"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="Nhập tên chức vụ..."
                                @keyup.enter="applyFilter"
                            />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Trạng thái</label>
                            <select
                                v-model="filters.status"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">Toàn bộ trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Tạm khóa</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                            @click="resetFilter"
                        >
                            Xóa Lọc
                        </button>
                        <button
                            type="button"
                            class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            @click="applyFilter"
                        >
                            Tìm Kiếm
                        </button>
                        <button
                            v-if="canManageAuthorityLevels"
                            type="button"
                            class="rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100"
                            @click="openAuthorityLevelModal"
                        >
                            Phân Cấp
                        </button>
                        <button
                            type="button"
                            class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 flex items-center justify-center gap-1"
                            @click="openCreateModal"
                        >
                            <span class="text-lg leading-none">+</span>
                            <span>Thêm Mới</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- DATA MATRIX -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50/50 border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5 text-xs font-semibold uppercase text-slate-500">Tên chức vụ</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Mức quyền hạn</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Quyền phân bổ</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Nhân viên</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Trạng thái</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="position in positions" :key="position.id" class="transition-colors hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-slate-900">{{ position.name }}</span>
                                        <span class="mt-0.5 truncate max-w-[250px] text-xs text-slate-500">{{ position.description || 'Không có mô tả' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700">
                                        {{ authorityLabelMap[position.authority_level] ?? 'Chưa thiết lập' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button
                                        v-if="position.capabilities?.length"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100"
                                        @click="openDetailModal(position)"
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        {{ position.capabilities.length }} Quyền
                                    </button>
                                    <span v-else class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-500">
                                        Chưa phân quyền
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        :class="position.employee_profiles_count > 0 ? 'text-slate-900 font-semibold' : 'text-slate-400'"
                                        class="text-sm"
                                    >
                                        {{ position.employee_profiles_count > 0 ? `${position.employee_profiles_count} NS` : '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex justify-center">
                                        <span
                                            :class="position.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        >
                                            {{ position.is_active ? 'Đang hoạt động' : 'Tạm khóa' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            type="button"
                                            class="rounded-lg px-2.5 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                                            @click="openDetailModal(position)"
                                        >Xem</button>
                                        <span class="text-slate-200">|</span>
                                        <button
                                            v-if="canEditPosition(position)"
                                            type="button"
                                            class="rounded-lg px-2.5 py-1.5 text-sm font-medium text-blue-600 transition hover:bg-blue-50 hover:text-blue-800"
                                            @click="openEditModal(position)"
                                        >Sửa</button>
                                        <span v-if="canTogglePosition(position)" class="text-slate-200">|</span>
                                        <button
                                            v-if="canTogglePosition(position)"
                                            type="button"
                                            class="rounded-lg px-2.5 py-1.5 text-sm font-medium transition"
                                            :class="position.is_active ? 'text-amber-600 hover:bg-amber-50 hover:text-amber-800' : 'text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800'"
                                            @click="toggleStatus(position)"
                                        >{{ position.is_active ? 'Khóa' : 'Mở' }}</button>
                                        <span v-if="canManageAuthorityLevels && position.employee_profiles_count === 0" class="text-slate-200">|</span>
                                        <button
                                            v-if="canManageAuthorityLevels && position.employee_profiles_count === 0"
                                            type="button"
                                            class="rounded-lg px-2.5 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-800"
                                            @click="confirmDelete(position)"
                                        >Xóa</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!positions.length">
                                <td colspan="6" class="px-5 py-12 text-center text-sm font-medium text-slate-500">
                                    {{ emptyMessage }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Modal (Create / Edit) -->
        <Modal :show="isFormModalOpen" @close="closeFormModal" max-width="3xl">
            <div class="flex max-h-[85vh] flex-col overflow-hidden bg-white">
                <div class="shrink-0 border-b border-slate-200 px-6 py-5">
                    <h2 class="text-lg font-bold text-slate-900">
                        {{ isEditing ? 'Chỉnh sửa chức vụ' : 'Tạo chức vụ mới' }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">Thiết lập thông tin và bộ quyền hạn tương ứng.</p>
                </div>

                <!-- Tabs -->
                <div class="shrink-0 flex w-full items-center border-b border-slate-200 px-6 gap-2">
                    <button
                        type="button"
                        class="relative px-2 py-4 text-sm font-medium transition-colors"
                        :class="activeTab === 'info' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-700'"
                        @click="activeTab = 'info'"
                    >
                        Cấu hình cơ bản
                        <div v-if="activeTab === 'info'" class="absolute bottom-0 left-0 right-0 h-0.5 rounded-t-lg bg-blue-600"></div>
                    </button>
                    <button
                        type="button"
                        class="relative ml-4 px-2 py-4 text-sm font-medium transition-colors"
                        :class="activeTab === 'capabilities' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-700'"
                        @click="activeTab = 'capabilities'"
                    >
                        Phân bổ quyền hạn
                        <span v-if="form.capabilities.length" class="ml-1.5 inline-flex items-center justify-center rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">
                            {{ form.capabilities.length }}
                        </span>
                        <div v-if="activeTab === 'capabilities'" class="absolute bottom-0 left-0 right-0 h-0.5 rounded-t-lg bg-blue-600"></div>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
                    <form @submit.prevent="submit" class="h-full">
                        <!-- Tab: Info -->
                        <div v-show="activeTab === 'info'" class="space-y-5 rounded-xl border border-slate-200 bg-white p-5">
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="col-span-1 md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-slate-700">
                                        Tên chức vụ <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm font-medium text-slate-900 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-400"
                                        placeholder="Ví dụ: Trưởng nhóm Kỹ thuật"
                                    />
                                    <div v-if="form.errors.name" class="mt-1.5 text-xs text-rose-600">{{ form.errors.name }}</div>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-slate-700">Mức quyền hạn</label>
                                    <select
                                        v-model="form.authority_level"
                                        class="h-11 w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 text-sm font-medium text-slate-900 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer"
                                    >
                                        <option value="">— Trống —</option>
                                        <option v-for="option in authorityOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.authority_level" class="mt-1.5 text-xs text-rose-600">{{ form.errors.authority_level }}</div>
                                    <div class="mt-2 text-xs text-slate-500">
                                        Hệ thống kiểm soát theo rank và capability đã chọn.
                                    </div>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-slate-700">Mô tả</label>
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none placeholder-slate-400"
                                        placeholder="Mô tả công việc và trách nhiệm..."
                                    />
                                </div>

                                <div class="col-span-1 md:col-span-2 pt-1">
                                    <label class="inline-flex cursor-pointer items-center gap-3">
                                        <input
                                            v-model="form.is_active"
                                            type="checkbox"
                                            class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer transition"
                                        />
                                        <span class="text-sm font-medium text-slate-700">Trạng thái hoạt động</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Capabilities -->
                        <div v-show="activeTab === 'capabilities'" class="space-y-6">
                            
                            <div v-if="canManageAuthorityLevels && canCreateCustomCapabilities" class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-semibold text-indigo-800">Quản lý định nghĩa quyền tùy chỉnh</div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50"
                                        @click="showCapabilityCreator = !showCapabilityCreator"
                                    >
                                        {{ showCapabilityCreator ? 'Thu gọn' : '+ Thêm quyền' }}
                                    </button>
                                </div>

                                <div v-show="showCapabilityCreator" class="mt-4 border-t border-indigo-100 pt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold text-indigo-900/70">Tên quyền</label>
                                        <input
                                            v-model="capabilityCreateForm.name"
                                            type="text"
                                            class="h-10 w-full rounded-xl border border-indigo-200 bg-white px-3 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            placeholder="Ex: Quản lý thiết bị"
                                        />
                                        <div v-if="capabilityCreateForm.errors.name" class="mt-1 text-xs text-rose-600">{{ capabilityCreateForm.errors.name }}</div>
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold text-indigo-900/70">Nhóm module</label>
                                        <input
                                            v-model="capabilityCreateForm.module"
                                            type="text"
                                            class="h-10 w-full rounded-xl border border-indigo-200 bg-white px-3 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            placeholder="custom"
                                        />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1.5 block text-xs font-semibold text-indigo-900/70">Mô tả chi tiết</label>
                                        <input
                                            v-model="capabilityCreateForm.description"
                                            type="text"
                                            class="h-10 w-full rounded-xl border border-indigo-200 bg-white px-3 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                        />
                                    </div>
                                    <div class="md:col-span-2 flex justify-end">
                                        <button
                                            type="button"
                                            class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50 inline-flex items-center justify-center"
                                            :disabled="capabilityCreateForm.processing"
                                            @click="submitNewCapability"
                                        >
                                            {{ capabilityCreateForm.processing ? 'Đang lưu...' : 'Thêm Quyền Mới' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div
                                    v-for="group in capabilityGroups"
                                    :key="group.group"
                                    class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col"
                                >
                                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-4 py-3 shrink-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">{{ group.icon }}</span>
                                            <span class="text-sm font-semibold text-slate-800">{{ group.group }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="rounded-md px-2 py-1 text-xs font-semibold text-blue-600 transition hover:bg-blue-50"
                                                @click="selectAllInGroup(group)"
                                            >Tất cả</button>
                                            <button
                                                type="button"
                                                class="rounded-md px-2 py-1 text-xs font-medium text-slate-500 transition hover:bg-slate-100"
                                                @click="clearAllInGroup(group)"
                                            >Xóa</button>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 divide-y divide-slate-50 p-2">
                                        <label
                                            v-for="item in group.items"
                                            :key="item.key"
                                            class="flex cursor-pointer items-start gap-3 rounded-lg px-3 py-2.5 transition-colors hover:bg-slate-50"
                                        >
                                            <div class="relative mt-0.5 flex shrink-0 items-center">
                                                <input
                                                    type="checkbox"
                                                    :checked="form.capabilities.includes(item.key)"
                                                    class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 transition focus:ring-blue-500"
                                                    @change="toggleCapability(item.key)"
                                                />
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-slate-800 transition-colors">{{ item.label }}</span>
                                                <span class="mt-0.5 text-xs text-slate-500 line-clamp-1" :title="item.desc">{{ item.desc }}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div v-if="form.errors.capabilities" class="text-sm font-medium text-rose-600">{{ form.errors.capabilities }}</div>
                        </div>
                    </form>
                </div>
                
                <div class="shrink-0 flex items-center justify-between border-t border-slate-200 bg-white px-6 py-4">
                    <div class="text-sm text-slate-600">
                        <span v-if="activeTab === 'capabilities'">Đã chọn <span class="font-bold text-slate-900">{{ form.capabilities.length }}</span> / {{ allCapabilities.length }} quyền</span>
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                            @click="closeFormModal"
                        >Hủy Bỏ</button>
                        <button
                            type="button"
                            class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95 disabled:opacity-60"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            {{ form.processing ? 'Đang xử lý...' : (isEditing ? 'Lưu Thay Đổi' : 'Xác Nhận Tạo') }}
                        </button>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Authority Level Modal -->
        <Modal :show="isAuthorityLevelModalOpen" @close="closeAuthorityLevelModal" max-width="2xl">
            <div class="flex flex-col bg-white">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h2 class="text-lg font-bold text-slate-900">Quản Lý Phân Cấp</h2>
                    <p class="mt-1 text-sm text-slate-500">Cấu hình cấp quản lý và thứ bậc trong hệ thống.</p>
                </div>

                <div class="p-6 bg-slate-50/50 space-y-6">
                    <form class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm" @submit.prevent="submitAuthorityLevel">
                        <div class="mb-4 text-sm font-semibold text-indigo-800">Thêm Cấp Bậc Mới</div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Thứ bậc (Rank)</label>
                                    <input
                                    v-model="authorityLevelForm.rank"
                                    type="number"
                                    min="1"
                                    class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm font-medium text-slate-900 outline-none transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Ex: 6"
                                />
                                <div v-if="authorityLevelForm.errors.rank" class="mt-1 text-xs text-rose-600">{{ authorityLevelForm.errors.rank }}</div>
                            </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Tên Định Danh</label>
                                    <input
                                        v-model="authorityLevelForm.name"
                                    type="text"
                                    class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm font-medium text-slate-900 outline-none transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Ex: Mức 6 - Phó tổng"
                                />
                                <div v-if="authorityLevelForm.errors.name" class="mt-1 text-xs text-rose-600">{{ authorityLevelForm.errors.name }}</div>
                                </div>
                        </div>
                        <div class="mt-5 flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input v-model="authorityLevelForm.is_active" type="checkbox" class="h-4 w-4 cursor-pointer rounded border-slate-300 text-indigo-600 transition focus:ring-indigo-500" />
                                <span class="text-sm font-medium text-slate-700">Kích hoạt ngay</span>
                            </label>
                            <button
                                type="submit"
                                class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                                :disabled="authorityLevelForm.processing"
                            >
                                {{ authorityLevelForm.processing ? 'Đang xử lý...' : 'Thêm Mức Này' }}
                            </button>
                        </div>
                    </form>

                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full text-left whitespace-nowrap">
                            <thead class="border-b border-slate-200 bg-slate-50">
                                <tr>
                                    <th class="px-5 py-3 text-xs font-semibold uppercase text-slate-500">Rank</th>
                                    <th class="px-5 py-3 text-xs font-semibold uppercase text-slate-500">Tên Mức</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">Trạng Thái</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="level in authorityLevelCatalog" :key="level.id" class="transition-colors hover:bg-slate-50/50">
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">#{{ level.rank }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-700">{{ level.name }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span
                                            :class="level.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                        >
                                            {{ level.is_active ? 'Kích hoạt' : 'Tạm khóa' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <button
                                            type="button"
                                            class="rounded-lg px-2.5 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                                            @click="toggleAuthorityLevel(level)"
                                        >
                                            {{ level.is_active ? 'Khóa' : 'Mở' }}
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!authorityLevelCatalog.length">
                                    <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Chưa có dữ liệu cấp bậc.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-slate-200 bg-white px-6 py-4 flex justify-end">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        @click="closeAuthorityLevelModal"
                    >Đóng Cửa Sổ</button>
                </div>
            </div>
        </Modal>

        <!-- Detail Modal -->
        <Modal :show="isDetailModalOpen" @close="isDetailModalOpen = false" max-width="3xl">
            <div v-if="selectedPosition" class="flex flex-col bg-white">
                
                <div class="border-b border-slate-200 px-6 py-6 flex items-start justify-between bg-slate-50/50">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ selectedPosition.name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Hồ sơ chi tiết chức vụ</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span
                            :class="selectedPosition.is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200'"
                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                        >
                            {{ selectedPosition.is_active ? 'Đang hoạt động' : 'Tạm khóa' }}
                        </span>
                        <span class="text-xs font-medium text-slate-500">
                            {{ selectedPosition.employee_profiles_count }} nhân sự
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1 space-y-5">
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                <h3 class="mb-3 text-xs font-semibold uppercase text-slate-500">Thông tin chung</h3>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-xs font-medium text-slate-400 mb-1">Mức Phân Cấp</div>
                                        <div class="inline-flex rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            {{ authorityLabelMap[selectedPosition.authority_level] ?? 'Vô Danh' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-slate-400 mb-1">Mô tả nhiệm vụ</div>
                                        <p class="text-sm text-slate-700">
                                            {{ selectedPosition.description || 'Chưa định nghĩa văn bản mô tả cụ thể.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                                <h3 class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                                    <span class="text-sm font-bold text-slate-800">Ma trận quyền hạn</span>
                                    <span class="inline-flex items-center justify-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                        {{ selectedPosition.capabilities?.length || 0 }} quyền
                                    </span>
                                </h3>

                                <div v-if="selectedPosition.capabilities?.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div
                                        v-for="group in capabilityGroups"
                                        :key="group.group"
                                        class="rounded-xl border border-slate-100 bg-slate-50/50 p-3"
                                    >
                                        <div class="mb-3 flex items-center gap-2 border-b border-slate-200/60 pb-2">
                                            <span class="text-lg">{{ group.icon }}</span>
                                            <span class="text-xs font-bold text-slate-700">{{ group.group }}</span>
                                        </div>
                                        <div class="space-y-2">
                                            <div
                                                v-for="item in group.items"
                                                :key="item.key"
                                            >
                                                <div v-if="hasCapability(selectedPosition.capabilities, item.key)" class="flex gap-2">
                                                    <div class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></div>
                                                    <span class="text-xs font-medium text-slate-700 leading-tight">
                                                        {{ item.label }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="py-8 text-center text-sm font-medium text-slate-400 rounded-xl border border-dashed border-slate-200 bg-slate-50">
                                    Chức vụ này chưa được cấp nhóm quyền nào.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                        @click="isDetailModalOpen = false"
                    >Đóng</button>
                    <button
                        type="button"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 flex items-center gap-2"
                        @click="() => { isDetailModalOpen = false; openEditModal(selectedPosition) }"
                    >
                        Chỉnh sửa
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
