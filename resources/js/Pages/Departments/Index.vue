<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/tables/DataTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import FormInputLabel from '@/components/ui/FormInputLabel.vue'
import FormInput from '@/components/ui/FormInput.vue'
import ErrorForm from '@/components/forms/ErrorForm.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import { EditIcon, TrashIcon } from '@/icons'
import Swal from 'sweetalert2'

const props = defineProps({
    departments: Array,
    users: Array,
})

const isModalOpen = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const form = useForm({
    name: '',
    description: '',
    manager_user_id: '',
    is_active: true
})

const columns = [
    { label: 'Tên phòng ban', key: 'name', sortable: true },
    { label: 'Trưởng phòng', key: 'manager.name', sortable: true },
    { label: 'Số nhân viên', key: 'employee_profiles_count', align: 'text-center' },
    { label: 'Mô tả', key: 'description' },
    { label: 'Trạng thái', key: 'is_active', align: 'text-center' }
]

const actions = [
    {
        icon: EditIcon,
        buttonProps: { class: 'mr-2 text-brand-500 hover:text-brand-600', title: 'Chỉnh sửa' },
        onClick: (item) => openEditModal(item)
    },
    {
        icon: TrashIcon,
        buttonProps: { class: 'text-error-500 hover:text-error-600', title: 'Xóa' },
        onClick: (item) => confirmDelete(item)
    }
]

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    isModalOpen.value = true
}

const openEditModal = (department) => {
    if (department.employee_profiles_count > 0) {
        Swal.fire({
            title: 'Không thể chỉnh sửa!',
            html: `Phòng ban <strong>${department.name}</strong> đang có <strong>${department.employee_profiles_count} nhân viên</strong>.<br>Không thể sửa khi phòng ban đang được sử dụng.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#f59e0b'
        })
        return
    }
    isEditing.value = true
    editingId.value = department.id
    form.name = department.name
    form.description = department.description
    form.manager_user_id = department.manager_user_id
    form.is_active = !!department.is_active
    isModalOpen.value = true
}

const submit = () => {
    if (isEditing.value) {
        form.put(route('departments.update', editingId.value), {
            onSuccess: () => {
                isModalOpen.value = false
                isEditing.value = false
                editingId.value = null
                form.reset()
                Swal.fire('Thành công', 'Đã cập nhật phòng ban', 'success')
            }
        })
    } else {
        form.post(route('departments.store'), {
            onSuccess: () => {
                isModalOpen.value = false
                form.reset()
                Swal.fire('Thành công', 'Đã tạo phòng ban mới', 'success')
            }
        })
    }
}

const confirmDelete = (department) => {
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: `Xóa phòng ban "${department.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Xóa ngay',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('departments.destroy', department.id), {
                onSuccess: () => Swal.fire('Đã xóa', 'Phòng ban đã được loại bỏ.', 'success')
            })
        }
    })
}
</script>

<template>
    <Head title="Quản lý Phòng ban"/>

    <AdminLayout>
        <PageBreadcrumb title="Quản lý Phòng ban" :items="[{ text: 'Tổ chức', link: null }, { text: 'Phòng ban', link: null }]" />

        <div class="mb-6 flex justify-end">
            <Button :onClick="openCreateModal">
                Thêm phòng ban mới
            </Button>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-900">
            <DataTable 
                :columns="columns" 
                :data="departments" 
                :actions="actions"
                emptyMessage="Chưa có dữ liệu phòng ban."
            >
                <template #cell-employee_profiles_count="{ item }">
                    <span :class="item.employee_profiles_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'"
                          class="rounded-full px-3 py-1 text-xs font-medium">
                        {{ item.employee_profiles_count > 0 ? item.employee_profiles_count + ' nhân viên' : 'Chưa sử dụng' }}
                    </span>
                </template>
                <template #cell-is_active="{ item }">
                    <span :class="item.is_active ? 'bg-success-100 text-success-700' : 'bg-error-100 text-error-700'" 
                          class="rounded-full px-3 py-1 text-xs font-medium">
                        {{ item.is_active ? 'Đang hoạt động' : 'Tạm khóa' }}
                    </span>
                </template>
            </DataTable>
        </div>

        <!-- Modal Form -->
        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ isEditing ? 'Chỉnh sửa phòng ban' : 'Tạo phòng ban mới' }}
                </h2>
                <form @submit.prevent="submit">
                    <div class="space-y-4">
                        <div>
                            <FormInput 
                                id="name" 
                                v-model="form.name" 
                                type="text" 
                                label="Tên phòng ban"
                                :required="true"
                                placeholder="Nhập tên phòng ban..."
                                class="mt-1 block w-full" 
                                :error="form.errors.name"
                            />
                        </div>

                        <div>
                            <FormSelect 
                                id="manager_user_id" 
                                v-model="form.manager_user_id" 
                                :options="users.map(u => ({ value: u.id, label: u.name }))"
                                label="Trưởng phòng"
                                placeholder="Chọn trưởng phòng"
                                :error="form.errors.manager_user_id"
                            />
                        </div>

                        <div>
                            <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Mô tả
                            </label>
                            <textarea 
                                id="description" 
                                v-model="form.description" 
                                class="w-full px-4 py-3 rounded-sm border border-gray-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" 
                                rows="3"
                                placeholder="Nhập mô tả..."
                            ></textarea>
                            <ErrorForm :message="form.errors.description" />
                        </div>

                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="is_active" 
                                v-model="form.is_active" 
                                class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900"
                            />
                            <label for="is_active" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Đang hoạt động</label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button 
                            type="button" 
                            @click="isModalOpen = false"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Hủy
                        </button>
                        <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" type="submit">
                            {{ isEditing ? 'Cập nhật' : 'Tạo mới' }}
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>
