<script setup>
import { computed, ref, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Modal from '@/components/ui/Modal.vue'
import FormSelect from '@/components/forms/FormSelect.vue'
import InputDate from '@/components/forms/InputDate.vue'

const props = defineProps({
  profile: { type: Object, required: true },
  passwordChangeOtpPending: { type: Boolean, default: false },
})

const page = usePage()
const authUser = computed(() => page.props.auth?.user || {})

// ─── TABS ──────────────────────────────────────────────────────────────────
const activeTab = ref('personal') // 'personal' | 'security'

// ─── FORMS ─────────────────────────────────────────────────────────────────
const profileForm = useForm({
  phone: props.profile.phone || '',
  date_of_birth: props.profile.date_of_birth || '',
  province_id: props.profile.province_id || '',
  ward_id: props.profile.ward_id || '',
  address_line: props.profile.address_line || '',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
  otp: '',
})

const avatarForm = useForm({
  avatar: null,
})

// ─── MODAL STATE ────────────────────────────────────────────────────────────
const isEditProfileOpen = ref(false)
const isChangePasswordOpen = ref(false)
const isEmailChangeOpen = ref(false)
const isPasswordOtpStep = ref(props.passwordChangeOtpPending)
const avatarInput = ref(null)

// ─── LOCATION DATA ──────────────────────────────────────────────────────────
const provinces = ref([])
const wards = ref([])
const isLoadingWards = ref(false)

const fetchProvinces = async () => {
    try {
        const response = await axios.get('/api/locations/provinces')
        provinces.value = response.data
    } catch (error) {
        console.error('Lỗi khi tải tỉnh/thành:', error)
    }
}

const fetchWards = async (provinceId) => {
    if (!provinceId) {
        wards.value = []
        return
    }
    isLoadingWards.value = true
    try {
        const response = await axios.get(`/api/locations/wards/${provinceId}`)
        wards.value = response.data
    } catch (error) {
        console.error('Lỗi khi tải xã/phường:', error)
    } finally {
        isLoadingWards.value = false
    }
}

const provinceOptions = computed(() => {
    return provinces.value.map(p => ({ value: p.id, label: p.name }))
})

const wardOptions = computed(() => {
    return wards.value.map(w => ({ value: w.id, label: w.name }))
})

watch(() => profileForm.province_id, (newVal, oldVal) => {
    if (newVal && newVal !== oldVal) {
        if (oldVal !== '') {
            profileForm.ward_id = ''
        }
        fetchWards(newVal)
    } else if (!newVal) {
        wards.value = []
        profileForm.ward_id = ''
    }
})

watch(isEditProfileOpen, (isOpen) => {
    if (isOpen) {
        fetchProvinces()
        if (profileForm.province_id) {
            fetchWards(profileForm.province_id)
        }
    }
})

watch(() => props.passwordChangeOtpPending, (isPending) => {
    isPasswordOtpStep.value = isPending
})

// ─── HELPERS ────────────────────────────────────────────────────────────────
const positionLabel = computed(() => {
  if (props.profile.position) return props.profile.position
  if (Number(props.profile.authority_level || 0) > 0) return `Rank ${props.profile.authority_level}`
  return '-'
})

const employmentStatusLabel = computed(() => ({
  active: 'Đang làm việc',
  inactive: 'Tạm nghỉ / Không hoạt động',
  terminated: 'Đã nghỉ việc',
  probation: 'Thử việc',
  on_leave: 'Nghỉ phép',
}[props.profile.employment_status] || props.profile.employment_status))

const employmentTypeLabel = computed(() => ({
  official: 'Chính thức',
  probation: 'Thử việc',
  intern: 'Thực tập sinh',
  collaborator: 'Cộng tác viên',
}[props.profile.employment_type] || props.profile.employment_type))

const currentShiftLabel = computed(() => {
  const shift = props.profile.current_shift
  if (!shift) return 'Chưa được phân ca'

  const timeRange = shift.start_time && shift.end_time
    ? `${shift.start_time} - ${shift.end_time}${shift.is_overnight ? ' (+1)' : ''}`
    : 'Chưa có khung giờ'

  return `${shift.shift_name} | ${timeRange}`
})

const currentShiftHint = computed(() => {
  const shift = props.profile.current_shift
  if (!shift) return 'Hiện tại chưa có ca làm áp dụng cho tài khoản này.'

  if (shift.source === 'assignment') {
    return `Đang lấy theo phân ca${shift.effective_from ? ` từ ${shift.effective_from}` : ''}${shift.effective_to ? ` đến ${shift.effective_to}` : ''}.`
  }

  return 'Đang hiển thị theo ca mặc định trong hồ sơ nhân viên.'
})

function formatCurrency(value) {
  if (!value) return '-'
  return `${new Intl.NumberFormat('vi-VN').format(Number(value))} VND`
}

const getAvatarUrl = (avatar) => {
  if (!avatar) return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(props.profile.name) + '&background=random&size=200'
  if (avatar.startsWith('http')) return avatar
  return avatar
}

const statusBadgeClass = (status) => {
  const map = {
    active: 'bg-emerald-100 text-emerald-700',
    pending: 'bg-amber-100 text-amber-700',
    blocked: 'bg-rose-100 text-rose-700',
    inactive: 'bg-gray-100 text-gray-700',
  }
  return map[status] || 'bg-gray-100 text-gray-700'
}

const statusLabel = (status) => {
  const map = {
    active: 'Đang hoạt động',
    pending: 'Đang chờ',
    blocked: 'Đã khóa',
    inactive: 'Không hoạt động',
  }
  return map[status] || status
}

// ─── ACTIONS ────────────────────────────────────────────────────────────────
const saveProfile = () => {
  profileForm.put(route('my-profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      isEditProfileOpen.value = false
      toast.success('Đã cập nhật thông tin cá nhân.')
    },
    onError: () => toast.error('Có lỗi xảy ra khi cập nhật.')
  })
}

const openChangePasswordModal = () => {
  isPasswordOtpStep.value = props.passwordChangeOtpPending
  passwordForm.clearErrors()

  if (props.passwordChangeOtpPending) {
    passwordForm.reset('otp')
  } else {
    passwordForm.reset()
  }

  isChangePasswordOpen.value = true
}

const closeChangePasswordModal = () => {
  isChangePasswordOpen.value = false
  passwordForm.clearErrors()

  if (props.passwordChangeOtpPending) {
    passwordForm.delete(route('password.change-otp.cancel'), {
      preserveScroll: true,
      onFinish: () => {
        passwordForm.reset()
        isPasswordOtpStep.value = false
      },
    })
    return
  }

  passwordForm.reset()
  isPasswordOtpStep.value = false
}

const requestPasswordOtp = () => {
  passwordForm.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      isPasswordOtpStep.value = true
      passwordForm.reset('current_password', 'password', 'password_confirmation', 'otp')
      toast.info('Đã gửi mã OTP đến email của bạn.')
    },
    onError: (errors) => {
      if (errors.current_password) toast.error(errors.current_password)
      else if (errors.password) toast.error(errors.password)
      else if (errors.otp) toast.error(errors.otp)
      else toast.error('Không thể gửi mã OTP. Vui lòng kiểm tra lại thông tin.')
    }
  })
}

const verifyPasswordOtp = () => {
  passwordForm.post(route('password.change-otp.verify'), {
    preserveScroll: true,
    onSuccess: () => {
      isChangePasswordOpen.value = false
      isPasswordOtpStep.value = false
      passwordForm.reset()
      toast.success('Đã đổi mật khẩu thành công.')
    },
    onError: (errors) => {
      if (errors.otp) toast.error(errors.otp)
      else toast.error('Không thể xác thực mã OTP.')
    }
  })
}

const resendPasswordOtp = () => {
  passwordForm.post(route('password.change-otp.resend'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset('otp')
      toast.info('Đã gửi lại mã OTP mới.')
    },
    onError: (errors) => {
      if (errors.otp) toast.error(errors.otp)
      else toast.error('Không thể gửi lại mã OTP.')
    }
  })
}

const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    avatarForm.avatar = file
    avatarForm.post(route('my-profile.avatar'), {
      preserveState: true,
      forceFormData: true,
      onSuccess: () => {
        toast.success('Đã cập nhật ảnh đại diện.')
        avatarForm.reset()
      },
      onError: (errors) => {
        console.error('Avatar upload error:', errors)
        if (errors.avatar) {
          toast.error(errors.avatar)
        } else {
          toast.error('Không thể tải ảnh lên. Vui lòng thử lại.')
        }
      }
    })
  }
}

const triggerAvatarUpload = () => {
  avatarInput.value?.click()
}
</script>

<template>
  <Head title="Hồ sơ cá nhân" />

  <AdminLayout>
    <PageBreadcrumb title="Hồ sơ cá nhân" :items="[
      { text: 'Tài khoản', link: null },
      { text: 'Hồ sơ cá nhân', link: null }
    ]" />

    <div class="mt-6 flex flex-col gap-6 lg:flex-row">
      <!-- Sidebar / Summary -->
      <div class="w-full space-y-6 lg:w-[380px]">
        <!-- Profile summary card -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
          <div class="relative h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
          <div class="relative px-6 pb-6 text-center">
            <div class="-mt-12 flex justify-center">
               <div class="group relative inline-block">
                 <img
                    :src="getAvatarUrl(profile.avatar)"
                    :alt="profile.name"
                    class="h-24 w-24 rounded-full border-4 border-white bg-white object-cover shadow-md transition group-hover:opacity-90"
                 />
                 <button
                    @click="triggerAvatarUpload"
                    class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full border border-gray-100 bg-white shadow-sm transition hover:bg-gray-50"
                    title="Đổi ảnh đại diện"
                 >
                    <span class="text-xs">📷</span>
                 </button>
                 <input ref="avatarInput" type="file" @change="onAvatarChange" class="hidden" accept="image/*" />
               </div>
            </div>
            <div class="mt-4">
              <h2 class="text-xl font-bold text-gray-900">{{ profile.name }}</h2>
              <p class="text-sm text-gray-500">{{ profile.email }}</p>
            </div>
            <div class="mt-4 flex flex-wrap justify-center gap-2">
              <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ positionLabel }}
              </span>
              <span :class="statusBadgeClass(profile.status)" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                {{ statusLabel(profile.status) }}
              </span>
            </div>
          </div>

          <div class="border-t border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between py-2 text-sm text-gray-600">
              <span class="font-medium">Mã nhân viên</span>
              <span class="font-semibold text-gray-900">{{ profile.employee_code || '-' }}</span>
            </div>
            <div class="flex items-center justify-between py-2 text-sm text-gray-600">
              <span class="font-medium">Phòng ban</span>
              <span class="font-semibold text-gray-900">{{ profile.department || '-' }}</span>
            </div>
            <div class="flex items-center justify-between py-2 text-sm text-gray-600">
              <span class="font-medium">Chức vụ</span>
              <span class="font-semibold text-gray-900">{{ profile.position || '-' }}</span>
            </div>
            <div class="py-2 text-sm text-gray-600">
              <div class="font-medium">Ca lam hien tai</div>
              <div class="mt-1 font-semibold text-gray-900">{{ currentShiftLabel }}</div>
              <div class="mt-1 text-xs text-gray-500">{{ currentShiftHint }}</div>
            </div>
          </div>
        </div>

        <!-- Quick Info -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
           <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Thời gian tham gia</h4>
           <div class="space-y-4">
             <div class="flex items-start gap-4">
               <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                 <span>📅</span>
               </div>
               <div>
                  <div class="text-xs text-gray-500">Ngày vào làm</div>
                  <div class="text-sm font-semibold text-gray-900">{{ profile.hire_date || '-' }}</div>
               </div>
             </div>
             <div class="flex items-start gap-4">
               <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                 <span>🌐</span>
               </div>
               <div>
                  <div class="text-xs text-gray-500">Đăng nhập cuối</div>
                  <div class="text-sm font-semibold text-gray-900">{{ profile.last_login_at || 'Chưa rõ' }}</div>
               </div>
             </div>
           </div>
        </div>
      </div>

      <!-- Main Content / Content -->
      <div class="flex-1 space-y-6">
        <!-- Tabs Nav -->
        <div class="flex gap-1 rounded-2xl bg-white p-1 shadow-sm border border-gray-200">
          <button
            @click="activeTab = 'personal'"
            :class="activeTab === 'personal' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'"
            class="flex-1 rounded-xl py-2.5 text-sm font-bold transition"
          >
            📋 Thông tin cá nhân
          </button>
          <button
            @click="activeTab = 'security'"
            :class="activeTab === 'security' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'"
            class="flex-1 rounded-xl py-2.5 text-sm font-bold transition"
          >
            🛡️ Bảo mật & Tài khoản
          </button>
        </div>
        <!-- Tab Content -->
        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300">
          <!-- Personal Info -->
          <div v-show="activeTab === 'personal'" class="space-y-8 animate-in fade-in slide-in-from-bottom-2">
            <div class="flex items-center justify-between">
               <h3 class="text-lg font-bold text-gray-900">Thông tin chi tiết</h3>
               <button
                  @click="isEditProfileOpen = true"
                  class="rounded-xl bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 transition hover:bg-blue-100"
               >
                 Sửa hồ sơ
               </button>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div class="space-y-1 rounded-xl p-4 transition hover:bg-gray-50 border border-transparent hover:border-gray-100">
                 <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Số điện thoại</div>
                 <div class="flex items-center gap-2">
                    <span class="text-base font-semibold text-gray-900">{{ profile.phone || 'Chưa cập nhật' }}</span>
                 </div>
              </div>

              <div class="space-y-1 rounded-xl p-4 transition hover:bg-gray-50 border border-transparent hover:border-gray-100">
                 <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Ngày sinh</div>
                <div class="text-base font-semibold text-gray-900">
                    {{ profile.date_of_birth 
                        ? new Date(profile.date_of_birth).toLocaleDateString('vi-VN') 
                        : 'Chưa cập nhật' }}
                </div>
              </div>

              <div class="space-y-1 rounded-xl p-4 transition hover:bg-gray-50 border border-transparent hover:border-gray-100 md:col-span-2">
                 <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Địa chỉ hiện tại</div>
                 <div class="text-base font-semibold text-gray-900">{{ profile.full_address || 'Chưa cập nhật' }}</div>
              </div>
              <div class="space-y-2 rounded-xl border border-blue-100 bg-blue-50 p-4 md:col-span-2">
                 <div class="text-xs font-bold uppercase tracking-wider text-blue-500">Ca lam dang ap dung</div>
                 <div class="text-base font-semibold text-blue-900">{{ currentShiftLabel }}</div>
                 <div class="text-sm text-blue-700">{{ currentShiftHint }}</div>
              </div>
            </div>
            <div class="pt-4 mt-4 border-t border-gray-100">
               <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Hợp đồng & Đãi ngộ (Chỉ xem)</h3>
               <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div class="rounded-xl bg-gray-50 p-4">
                     <div class="text-xs font-medium text-gray-500">Mức lương cơ bản</div>
                     <div class="mt-1 text-base font-bold text-blue-700">{{ formatCurrency(profile.base_salary) }}</div>
                  </div>
                  <div class="rounded-xl bg-gray-50 p-4">
                     <div class="text-xs font-medium text-gray-500">Loại nhân sự</div>
                     <div class="mt-1 text-base font-semibold text-gray-900">{{ employmentTypeLabel }}</div>
                  </div>
                  <div class="rounded-xl bg-gray-50 p-4">
                     <div class="text-xs font-medium text-gray-500">Trạng thái công tác</div>
                     <div class="mt-1 text-base font-semibold text-gray-900">{{ employmentStatusLabel }}</div>
                  </div>
               </div>
            </div>
          </div>

          <!-- Security & Account -->
          <div v-show="activeTab === 'security'" class="space-y-8 animate-in fade-in slide-in-from-bottom-2">
             <h3 class="text-lg font-bold text-gray-900">Thiết lập bảo mật</h3>

             <div class="space-y-4">
                <!-- Email row -->
                <div class="flex items-center justify-between rounded-xl border border-gray-200 p-5 transition hover:shadow-md">
                   <div class="flex items-center gap-4">
                      <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                         <span>📧</span>
                      </div>
                      <div>
                         <div class="text-sm font-bold text-gray-900">Email nhận thông báo</div>
                         <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            {{ profile.email }}
                            <span class="rounded bg-emerald-100 px-1 py-0.5 text-[10px] font-bold text-emerald-700">ĐÃ XÁC THỰC</span>
                         </div>
                      </div>
                   </div>
                   <!-- Temporarily disabled as requested "đổ dữ liệu" -->
                   <button class="text-sm font-bold text-gray-400 cursor-not-allowed">Thay đổi</button>
                </div>

                <!-- Password row -->
                <div class="flex items-center justify-between rounded-xl border border-gray-200 p-5 transition hover:shadow-md">
                   <div class="flex items-center gap-4">
                      <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                         <span>🔒</span>
                      </div>
                      <div>
                         <div class="text-sm font-bold text-gray-900">Mật khẩu hệ thống</div>
                         <div class="text-xs text-gray-500">Đã cập nhật gần đây</div>
                      </div>
                   </div>
                   <button
                       @click="openChangePasswordModal"
                      class="text-sm font-bold text-blue-600 hover:text-blue-700 transition"
                   >Đổi mật khẩu</button>
                </div>
             </div>

             <div class="rounded-xl bg-orange-50 border border-orange-100 p-5">
                <div class="flex gap-3">
                   <span class="text-lg">⚠️</span>
                   <div>
                      <div class="text-sm font-bold text-orange-800">Lưu ý bảo mật</div>
                      <p class="mt-1 text-xs leading-relaxed text-orange-700">
                        Không chia sẻ mật khẩu của bạn cho bất kỳ ai. Hệ thống HRM không bao giờ yêu cầu bạn cung cấp mật khẩu qua điện thoại hoặc email.
                      </p>
                   </div>
                </div>
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── MODAL: EDIT PROFILE ──────────────────────────────────────────────── -->
    <Modal :show="isEditProfileOpen" @close="isEditProfileOpen = false" max-width="md">
        <div class="p-6">
            <h2 class="mb-4 text-xl font-bold text-gray-900">Cập nhật thông tin</h2>
            <form @submit.prevent="saveProfile" class="space-y-4">
               <div>
                  <label class="mb-1 block text-sm font-bold text-gray-700">Số điện thoại</label>
                  <input
                    v-model="profileForm.phone"
                    type="text"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 outline-none transition focus:border-blue-500"
                  />
                  <div v-if="profileForm.errors.phone" class="mt-1 text-xs text-rose-500">{{ profileForm.errors.phone }}</div>
               </div>

               <div>
                  <InputDate
                    v-model="profileForm.date_of_birth"
                    label="Ngày sinh"
                    placeholder="Chọn ngày sinh..."
                    :error="profileForm.errors.date_of_birth ? [profileForm.errors.date_of_birth] : null"
                  />
               </div>

               <div>
                  <FormSelect
                    v-model="profileForm.province_id"
                    label="Tỉnh / Thành phố"
                    placeholder="Chọn tỉnh/thành..."
                    :options="provinceOptions"
                    :error="profileForm.errors.province_id ? [profileForm.errors.province_id] : null"
                    searchable
                    required
                  />
               </div>

               <div>
                  <FormSelect
                    v-model="profileForm.ward_id"
                    label="Phường / Xã"
                    placeholder="Chọn xã/phường..."
                    :options="wardOptions"
                    :disabled="!profileForm.province_id"
                    :loading="isLoadingWards"
                    :error="profileForm.errors.ward_id ? [profileForm.errors.ward_id] : null"
                    searchable
                    required
                  />
               </div>

               <div>
                  <label class="mb-1 block text-sm font-bold text-gray-700">Số nhà / Tên đường</label>
                  <input
                    v-model="profileForm.address_line"
                    type="text"
                    placeholder="VD: 123 Đường ABC..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 outline-none transition focus:border-blue-500"
                  />
                  <div v-if="profileForm.errors.address_line" class="mt-1 text-xs text-rose-500">{{ profileForm.errors.address_line }}</div>
               </div>

               <div class="flex justify-end gap-3 pt-2">
                  <button
                    type="button"
                    @click="isEditProfileOpen = false"
                    class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-bold text-gray-700 transition hover:bg-gray-50"
                  >Hủy</button>
                  <button
                    type="submit"
                    :disabled="profileForm.processing"
                    class="rounded-xl bg-blue-600 px-8 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:opacity-50"
                  >Lưu thay đổi</button>
               </div>
            </form>

        </div>
    </Modal>

    <!-- ── MODAL: CHANGE PASSWORD ────────────────────────────────────────────── -->
    <Modal :show="isChangePasswordOpen" @close="closeChangePasswordModal" max-width="md">
        <div class="p-6">
            <h2 class="mb-4 text-xl font-bold text-gray-900">Đổi mật khẩu mới</h2>
            <form v-if="!isPasswordOtpStep" @submit.prevent="requestPasswordOtp" class="space-y-4">
               <p class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                  Hệ thống sẽ gửi mã OTP đến email <strong>{{ profile.email }}</strong> trước khi cập nhật mật khẩu.
               </p>
               <div>
                  <label class="mb-1 block text-sm font-bold text-gray-700">Mật khẩu hiện tại</label>
                  <input
                    v-model="passwordForm.current_password"
                    type="password"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 outline-none transition focus:border-blue-500"
                  />
                  <div v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-rose-500">{{ passwordForm.errors.current_password }}</div>
               </div>

               <div>
                  <label class="mb-1 block text-sm font-bold text-gray-700">Mật khẩu mới</label>
                  <input
                    v-model="passwordForm.password"
                    type="password"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 outline-none transition focus:border-blue-500"
                  />
                  <div v-if="passwordForm.errors.password" class="mt-1 text-xs text-rose-500">{{ passwordForm.errors.password }}</div>
               </div>

               <div>
                  <label class="mb-1 block text-sm font-bold text-gray-700">Nhập lại mật khẩu mới</label>
                  <input
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 outline-none transition focus:border-blue-500"
                  />
                  <div v-if="passwordForm.errors.password_confirmation" class="mt-1 text-xs text-rose-500">{{ passwordForm.errors.password_confirmation }}</div>
               </div>

               <div v-if="passwordForm.errors.otp" class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-600">
                  {{ passwordForm.errors.otp }}
               </div>

               <div class="flex justify-end gap-3 pt-2">
                  <button
                    type="button"
                    @click="closeChangePasswordModal"
                    class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-bold text-gray-700 transition hover:bg-gray-50"
                  >Hủy</button>
                  <button
                    type="submit"
                    :disabled="passwordForm.processing"
                    class="rounded-xl bg-indigo-600 px-8 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                  >Gửi mã OTP</button>
               </div>
            </form>

            <form v-else @submit.prevent="verifyPasswordOtp" class="space-y-4">
               <p class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                  Nhập mã OTP đã được gửi đến <strong>{{ profile.email }}</strong> để xác nhận đổi mật khẩu.
               </p>

               <div>
                  <label class="mb-1 block text-sm font-bold text-gray-700">Mã OTP</label>
                  <input
                    v-model="passwordForm.otp"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    placeholder="123456"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-center text-lg tracking-[0.4em] outline-none transition focus:border-blue-500"
                  />
                  <div v-if="passwordForm.errors.otp" class="mt-1 text-xs text-rose-500">{{ passwordForm.errors.otp }}</div>
               </div>

               <div class="flex justify-between gap-3 pt-2">
                  <button
                    type="button"
                    @click="resendPasswordOtp"
                    :disabled="passwordForm.processing"
                    class="rounded-xl border border-indigo-200 px-5 py-2.5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50 disabled:opacity-50"
                  >Gửi lại OTP</button>
                  <div class="flex gap-3">
                    <button
                      type="button"
                      @click="closeChangePasswordModal"
                      class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-bold text-gray-700 transition hover:bg-gray-50"
                    >Hủy</button>
                    <button
                      type="submit"
                      :disabled="passwordForm.processing || passwordForm.otp.length !== 6"
                      class="rounded-xl bg-indigo-600 px-8 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                    >Xác nhận đổi mật khẩu</button>
                  </div>
               </div>
            </form>
        </div>
    </Modal>
  </AdminLayout>
</template>

<style scoped>
.animate-in {
  animation: slide-in-from-bottom 0.4s ease-out;
}

@keyframes slide-in-from-bottom {
  from {
    transform: translateY(10px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
