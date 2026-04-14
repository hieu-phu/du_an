<template>
    <CustomModal
        v-if="modelValue"
        :title="isEditMode ? 'Chỉnh sửa nhân sự' : 'Thêm nhân sự mới'"
        @close="close"
        :custom_class="modalClasses"
    >
        <template #body>
            <form @submit.prevent="submitForm">
                <div class="modal-body max-h-[calc(85vh-140px)] overflow-y-auto px-6 py-5">
                    <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        Mã nhân viên sẽ được hệ thống tự động tạo khi lưu.
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-x-5 gap-y-4 md:grid-cols-2">
                        <FormInput v-model="form.name" label="Họ và tên" placeholder="Nhập họ và tên" :required="true" :error="form.errors.name" />
                        <FormInput v-model="form.email" label="Email Gmail" type="email" placeholder="Nhập email Gmail" :required="true" :error="form.errors.email" />
                        <FormInput v-model="form.phone" label="Số điện thoại" placeholder="Nhập số điện thoại" :required="true" :error="form.errors.phone" />
                        <InputDate v-model="form.date_of_birth" label="Ngày sinh" placeholder="Chọn ngày sinh" :error="form.errors.date_of_birth" />
                        <InputDate v-model="form.hire_date" label="Ngày vào làm" placeholder="Chọn ngày vào làm" :required="true" :error="form.errors.hire_date" />
                        <FormInput
                            v-model="salaryDisplay"
                            label="Lương cơ bản"
                            type="text"
                            placeholder="Nhập lương cơ bản"
                            :error="form.errors.base_salary"
                            unit="VND"
                            @update:modelValue="handleSalaryInput"
                        />
                        <InputDate
                            v-if="form.employment_status === 'terminated'"
                            v-model="form.termination_date"
                            label="Ngày nghỉ việc"
                            placeholder="Chọn ngày nghỉ việc"
                            :required="true"
                            :error="form.errors.termination_date"
                        />
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-x-5 gap-y-4 md:grid-cols-2">
                        <FormSelect
                            id="department_id"
                            v-model="form.department_id"
                            :options="departmentOptions"
                            label="Phòng ban"
                            :required="true"
                            placeholder="Chọn phòng ban"
                            :error="form.errors.department_id"
                        />
                        <FormSelect
                            id="position_id"
                            v-model="form.position_id"
                            :options="positionOptions"
                            label="Chức vụ"
                            :required="true"
                            placeholder="Chọn chức vụ"
                            :error="form.errors.position_id"
                        />
                        <FormSelect
                            id="role_name"
                            v-model="form.role_name"
                            :options="roleOptions"
                            label="Quyền tài khoản"
                            :required="true"
                            placeholder="Chọn quyền tài khoản"
                            :error="form.errors.role_name"
                        />
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Trạng thái tài khoản</label>
                            <select
                                v-model="form.status"
                                :disabled="form.employment_status === 'terminated'"
                                class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:disabled:bg-gray-700"
                            >
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Ngừng hoạt động</option>
                                <option value="pending">Đang chờ</option>
                                <option value="blocked">Đã khóa</option>
                            </select>
                            <ErrorForm :message="form.errors.status" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Trạng thái làm việc</label>
                            <select v-model="form.employment_status" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                                <option value="active">Đang làm việc</option>
                                <option value="inactive">Tạm ngừng</option>
                                <option value="terminated">Nghỉ việc</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                Nghỉ việc sẽ tự động khóa tài khoản và yêu cầu ngày nghỉ việc.
                            </p>
                            <ErrorForm :message="form.errors.employment_status" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Loại nhân sự / giai đoạn làm việc</label>
                            <select v-model="form.employment_type" class="w-full rounded-sm border border-gray-300 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                                <option value="probation">Thử việc</option>
                                <option value="official">Chính thức</option>
                                <option value="intern">Thực tập</option>
                                <option value="collaborator">Cộng tác viên</option>
                            </select>
                            <ErrorForm :message="form.errors.employment_type" />
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-x-5 gap-y-4 md:grid-cols-2">
                        <FormInput v-model="form.password" label="Mật khẩu" type="password" placeholder="Nhập mật khẩu" :required="!isEditMode" :error="form.errors.password" />
                        <FormInput v-model="form.password_confirmation" label="Xác nhận mật khẩu" type="password" placeholder="Nhập lại mật khẩu" :required="!isEditMode" />
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-x-5 gap-y-4 md:grid-cols-2">
                        <FormSelect
                            id="province_id"
                            v-model="form.province_id"
                            :options="provinceOptions"
                            label="Tỉnh / Thành phố"
                            placeholder="Chọn tỉnh/thành"
                            :error="form.errors.province_id"
                            @update:modelValue="handleProvinceChange"
                        />
                        <FormSelect
                            id="ward_id"
                            v-model="form.ward_id"
                            :options="wardOptions"
                            label="Phường / Xã"
                            placeholder="Chọn phường/xã"
                            :error="form.errors.ward_id"
                            :disabled="!form.province_id"
                        />
                        <div class="md:col-span-2">
                            <FormInput
                                v-model="form.address_line"
                                label="Địa chỉ cụ thể (Số nhà, tên đường...)"
                                placeholder="Nhập số nhà, tên đường..."
                                :error="form.errors.address_line"
                            />
                        </div>
                    </div>
                </div>

                <div class="modal-footer flex flex-shrink-0 items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <button type="button" @click="close" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        Hủy bỏ
                    </button>
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                        {{ isEditMode ? 'Cập nhật' : 'Tạo mới' }}
                    </button>
                </div>
            </form>
        </template>
    </CustomModal>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import CustomModal from '@/components/modals/CustomModal.vue'
import FormInput from '@/components/ui/FormInput.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import ErrorForm from '@/components/forms/ErrorForm.vue'
import InputDate from '@/components/forms/InputDate.vue'

const props = defineProps({
    modelValue: Boolean,
    isEditMode: { type: Boolean, default: false },
    userData: { type: Object, default: null },
    departments: { type: Array, default: () => [] },
    positions: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
    provinces: { type: Array, default: () => [] },
    storeRoute: { type: String, default: '' },
    updateRoute: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue', 'success'])
const page = usePage()
const isAdmin = computed(() => (page.props.auth?.user?.roles || []).includes('admin'))

const modalClasses = [
    'relative', 'w-full', 'max-w-[900px]',
    'flex', 'flex-col', 'rounded-xl',
    'bg-white', 'dark:bg-gray-900',
    'overflow-hidden', 'max-h-[90vh]', 'md:max-h-[85vh]',
    'shadow-2xl'
]

const departmentOptions = computed(() => props.departments.map((item) => ({
    value: item.id,
    label: item.name,
})))

const positionOptions = computed(() => props.positions.map((item) => ({
    value: item.id,
    label: item.name,
})))

const provinceOptions = computed(() => props.provinces.map((item) => ({
    value: item.id,
    label: item.name,
})))

const wardOptions = ref([])

const roleOptions = computed(() => props.roles.map((item) => ({
    value: item.name,
    label: getRoleLabel(item.name),
})))

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    province_id: '',
    ward_id: '',
    address_line: '',
    status: 'active',
    date_of_birth: '',
    hire_date: '',
    termination_date: '',
    department_id: '',
    position_id: '',
    base_salary: '',
    employment_status: 'active',
    employment_type: 'official',
    role_name: 'employee',
    avatar: null,
})

const salaryDisplay = ref('')
const lastManualStatus = ref('active')

const getRoleLabel = (roleName) => ({
    admin: 'Quản trị viên',
    hr: 'Nhân sự',
    employee: 'Nhân viên',
}[roleName] || roleName)

const fetchWards = async (provinceId) => {
    if (!provinceId) return
    try {
        const response = await axios.get(`/api/locations/wards/${provinceId}`)
        wardOptions.value = response.data.map(w => ({ value: w.id, label: w.name }))
    } catch (error) {
        console.error('Error fetching wards:', error)
    }
}

const handleProvinceChange = (val) => {
    form.ward_id = ''
    wardOptions.value = []
    if (val) fetchWards(val)
}

const formatNumber = (value) => new Intl.NumberFormat('vi-VN').format(Number(value))

const handleSalaryInput = (value) => {
    const digitsOnly = String(value ?? '').replace(/\D/g, '')
    form.base_salary = digitsOnly
    salaryDisplay.value = digitsOnly ? formatNumber(digitsOnly) : ''
}

const resetForm = () => {
    form.reset()
    form.clearErrors()
    form.status = 'active'
    form.employment_status = 'active'
    form.employment_type = 'official'
    form.role_name = 'employee'
    form.department_id = ''
    form.position_id = ''
    salaryDisplay.value = ''
    lastManualStatus.value = 'active'
}

const populateForm = (user) => {
    form.name = user.name || ''
    form.email = user.email || ''
    form.phone = user.phone || ''
    form.province_id = user.province_id || ''
    form.ward_id = user.ward_id || ''
    form.address_line = user.address_line || ''

    if (form.province_id) fetchWards(form.province_id)

    form.status = user.status || 'active'
    form.date_of_birth = user.date_of_birth || ''
    form.hire_date = user.hire_date || ''
    form.termination_date = user.termination_date || ''
    form.department_id = user.department_id || ''
    form.position_id = user.position_id || ''
    form.base_salary = user.base_salary ? String(Number(user.base_salary)) : ''
    salaryDisplay.value = form.base_salary ? formatNumber(form.base_salary) : ''
    form.employment_status = user.employment_status || 'active'
    form.employment_type = user.employment_type || 'official'
    form.role_name = user.role_name || 'employee'
    form.password = ''
    form.password_confirmation = ''
    lastManualStatus.value = form.status === 'blocked' && form.employment_status === 'terminated'
        ? 'active'
        : form.status
}

watch(() => props.modelValue, (isOpen) => {
    if (!isOpen) return

    if (props.isEditMode && props.userData) {
        populateForm(props.userData)
        return
    }

    resetForm()
})

watch(() => form.status, (status) => {
    if (form.employment_status !== 'terminated') {
        lastManualStatus.value = status
    }
})

watch(() => form.employment_status, (status, previousStatus) => {
    if (status === 'terminated') {
        if (previousStatus !== 'terminated') {
            lastManualStatus.value = form.status
        }

        form.status = 'blocked'
        return
    }

    form.termination_date = ''

    if (previousStatus === 'terminated' && form.status === 'blocked') {
        form.status = lastManualStatus.value || 'active'
    }
})

const close = () => {
    emit('update:modelValue', false)
    resetForm()
}

const submitForm = () => {
    const payload = {
        ...form.data(),
        base_salary: form.base_salary === '' ? null : Number(form.base_salary),
    }

    if (props.isEditMode && props.userData) {
        const updateUrl = props.updateRoute.replace(':id', props.userData.id)

        form.transform(() => ({
            ...payload,
            _method: 'PUT',
        })).post(updateUrl, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Cập nhật nhân sự thành công!')
                close()
                emit('success')
            },
            onError: () => toast.error('Vui lòng kiểm tra lại thông tin!')
        })
        return
    }

    form.transform(() => payload).post(props.storeRoute, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(isAdmin.value ? 'Thêm mới thành công!' : 'Đã gửi yêu cầu cho Admin duyệt!')
            close()
            emit('success')
        },
        onError: () => toast.error('Vui lòng kiểm tra lại thông tin!')
    })
}
</script>
