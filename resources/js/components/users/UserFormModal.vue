<template>
    <CustomModal v-if="modelValue" :title="isEditMode ? 'Chỉnh sửa nhân sự' : 'Thêm nhân sự mới'" @close="close"
        :custom_class="modalClasses">
        <template #body>
            <form @submit.prevent="submitForm">
                <div class="modal-body overflow-y-auto px-6 py-5 max-h-[calc(85vh-140px)]">
                    <!-- Avatar upload -->
                    <div class="flex justify-center mb-6">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-3 border-gray-200 dark:border-gray-700 shadow-lg">
                                <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar"
                                    class="w-full h-full object-cover" />
                                <div v-else
                                    class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                                    {{ getInitials(form.name) }}
                                </div>
                            </div>
                            <label
                                class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center cursor-pointer shadow-md transition-all duration-200 group-hover:scale-110">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <input type="file" accept="image/*" class="hidden" @change="onAvatarChange" />
                            </label>
                        </div>
                    </div>

                    <!-- Thông tin cơ bản -->
                    <div class="space-y-1 mb-5">
                        <h5
                            class="text-sm font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                            Thông tin cơ bản
                        </h5>
                        <div class="h-px bg-gradient-to-r from-blue-500/30 to-transparent"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4 mb-6">
                        <FormInput v-model="form.name" label="Họ và tên" placeholder="Nhập họ và tên" :required="true"
                            :error="form.errors.name" />

                        <FormInput v-model="form.email" label="Email" type="email" placeholder="Nhập email"
                            :required="true" :error="form.errors.email" />

                        <FormInput v-model="form.phone" label="Số điện thoại" placeholder="Nhập số điện thoại"
                            :required="true" :error="form.errors.phone" />

                        <div class="w-full">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Trạng thái <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.status"
                                class="w-full px-4 py-3 rounded-sm border bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                :class="form.errors.status ? 'border-red-500' : 'border-gray-300 dark:border-gray-700'">
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Ngừng hoạt động</option>
                                <option value="pending">Đang chờ</option>
                                <option value="blocked">Đã khóa</option>
                            </select>
                            <ErrorForm :message="form.errors.status" />
                        </div>
                    </div>

                    <!-- Mật khẩu -->
                    <div class="space-y-1 mb-5">
                        <h5
                            class="text-sm font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-4 bg-orange-500 rounded-full"></span>
                            {{ isEditMode ? 'Đổi mật khẩu (để trống nếu không đổi)' : 'Mật khẩu' }}
                        </h5>
                        <div class="h-px bg-gradient-to-r from-orange-500/30 to-transparent"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4 mb-6">
                        <FormInput v-model="form.password" label="Mật khẩu" type="password"
                            placeholder="Nhập mật khẩu" :required="!isEditMode" :error="form.errors.password" />

                        <FormInput v-model="form.password_confirmation" label="Xác nhận mật khẩu" type="password"
                            placeholder="Nhập lại mật khẩu" :required="!isEditMode" />
                    </div>

                    <!-- Thông tin bổ sung -->
                    <div class="space-y-1 mb-5">
                        <h5
                            class="text-sm font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-4 bg-green-500 rounded-full"></span>
                            Thông tin bổ sung
                        </h5>
                        <div class="h-px bg-gradient-to-r from-green-500/30 to-transparent"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-y-4 mb-6">
                        <div class="w-full">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Địa chỉ (nếu có)
                            </label>
                            <textarea v-model="form.address" rows="2" placeholder="Nhập địa chỉ"
                                class="w-full px-4 py-3 rounded-sm border bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                :class="form.errors.address ? 'border-red-500' : 'border-gray-300 dark:border-gray-700'"></textarea>
                            <ErrorForm :message="form.errors.address" />
                        </div>
                    </div>
                </div>

                <!-- Footer buttons - Fixed -->
                <div
                    class="modal-footer flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" @click="close"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        Hủy bỏ
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm hover:shadow transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ isEditMode ? 'Cập nhật' : 'Tạo mới' }}
                    </button>
                </div>
            </form>
        </template>
    </CustomModal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'

import CustomModal from '@/components/modals/CustomModal.vue'
import FormInput from '@/components/ui/FormInput.vue'
import ErrorForm from '@/components/forms/ErrorForm.vue'

const props = defineProps({
    modelValue: Boolean,
    isEditMode: { type: Boolean, default: false },
    userData: { type: Object, default: null },
    storeRoute: { type: String, default: '' },
    updateRoute: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue', 'success'])

const modalClasses = [
    "relative", "w-full", "max-w-[720px]",
    "flex", "flex-col", "rounded-xl",
    "bg-white", "dark:bg-gray-900",
    "overflow-hidden", "max-h-[90vh]", "md:max-h-[85vh]",
    "shadow-2xl"
]

const avatarPreview = ref(null)
const avatarFile = ref(null)

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    address: '',
    status: 'active',
    avatar: null,
})

const getInitials = (name = '') => {
    const parts = name.trim().split(' ')
    if (!parts[0]) return '?'
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase()
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

const onAvatarChange = (event) => {
    const file = event.target.files[0]
    if (!file) return

    if (file.size > 2 * 1024 * 1024) {
        toast.error('Ảnh đại diện không được vượt quá 2MB')
        return
    }

    avatarFile.value = file
    form.avatar = file
    avatarPreview.value = URL.createObjectURL(file)
}

const resetForm = () => {
    form.reset()
    form.clearErrors()
    avatarPreview.value = null
    avatarFile.value = null
}

const populateForm = (user) => {
    if (!user) return
    form.name = user.name || ''
    form.email = user.email || ''
    form.phone = user.phone || ''
    form.address = user.address || ''
    form.status = user.status || 'active'
    form.password = ''
    form.password_confirmation = ''
    form.avatar = null

    if (user.avatar) {
        avatarPreview.value = user.avatar.startsWith('http')
            ? user.avatar
            : `/storage/${user.avatar}`
    } else {
        avatarPreview.value = null
    }
}

watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        if (props.isEditMode && props.userData) {
            populateForm(props.userData)
        } else {
            resetForm()
        }
    }
})

watch(() => props.userData, (user) => {
    if (props.modelValue && props.isEditMode && user) {
        populateForm(user)
    }
})

const close = () => {
    emit('update:modelValue', false)
    resetForm()
}

const submitForm = () => {
    if (props.isEditMode && props.userData) {
        const updateUrl = props.updateRoute.replace(':id', props.userData.id)

        form.transform(data => {
            data._method = 'PUT'
            return data
        }).post(updateUrl, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Cập nhật nhân sự thành công!')
                close()
                emit('success')
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error(errors.error)
                } else {
                    toast.error('Vui lòng kiểm tra lại thông tin!')
                }
            }
        })
    } else {
        form.post(props.storeRoute, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Thêm nhân sự mới thành công!')
                close()
                emit('success')
            },
            onError: (errors) => {
                if (errors.error) {
                    toast.error(errors.error)
                } else {
                    toast.error('Vui lòng kiểm tra lại thông tin!')
                }
            }
        })
    }
}
</script>
