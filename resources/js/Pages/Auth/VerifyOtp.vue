<script setup>
import { useForm } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

const props = defineProps({
    email: {
        type: String,
        default: '',
    },
    status: {
        type: String,
        default: null,
    },
    title: {
        type: String,
        default: 'Xác nhận mã OTP',
    },
    description: {
        type: String,
        default: 'Chúng tôi đã gửi mã xác nhận 6 số đến email',
    },
    submitRoute: {
        type: String,
        required: true,
    },
    resendRoute: {
        type: String,
        required: true,
    },
    changeRoute: {
        type: String,
        required: true,
    },
    changeLabel: {
        type: String,
        default: 'Thay đổi email khác',
    },
})

const form = useForm({
    email: props.email,
    otp: '',
})

const otpInput = ref(null)
const cooldown = ref(60)
const canResend = ref(false)
let timerId = null

const startCooldown = () => {
    canResend.value = false
    cooldown.value = 60

    if (timerId) {
        clearInterval(timerId)
    }

    timerId = setInterval(() => {
        cooldown.value--

        if (cooldown.value <= 0) {
            canResend.value = true
            clearInterval(timerId)
            timerId = null
        }
    }, 1000)
}

onMounted(() => {
    otpInput.value?.focus()
    startCooldown()
})

const submit = () => {
    form.post(props.submitRoute)
}

const handleResend = () => {
    if (!canResend.value) return

    form.post(props.resendRoute, {
        preserveScroll: true,
        onSuccess: () => {
            startCooldown()
        },
    })
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 mx-auto">
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-blue-100 mb-4">
                    <svg class="h-7 w-7 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ title }}</h2>
                <p class="text-sm text-gray-600">
                    {{ description }} <span class="font-semibold text-gray-900">{{ email }}</span>
                </p>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-100">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg">
                    {{ status }}
                </div>

                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <label for="otp" class="block text-sm font-semibold text-gray-700 mb-2">Mã xác nhận (OTP)</label>
                        <input
                            id="otp"
                            ref="otpInput"
                            v-model="form.otp"
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6"
                            placeholder="123456"
                            class="block w-full text-center text-2xl tracking-[0.5em] font-bold px-4 py-4 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            :class="form.errors.otp ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                            required
                        />
                        <p v-if="form.errors.otp" class="text-red-500 text-sm mt-1">
                            {{ Array.isArray(form.errors.otp) ? form.errors.otp[0] : form.errors.otp }}
                        </p>
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                            {{ Array.isArray(form.errors.email) ? form.errors.email[0] : form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing || form.otp.length !== 6"
                            class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.01] active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Đang xác nhận...' : 'Tiếp tục' }}</span>
                        </button>
                    </div>

                    <div class="text-center space-y-4">
                        <p class="text-sm text-gray-500">
                            Không nhận được mã?
                            <button
                                type="button"
                                @click="handleResend"
                                :disabled="!canResend || form.processing"
                                class="font-medium transition duration-200"
                                :class="canResend ? 'text-blue-600 hover:underline' : 'text-gray-400 cursor-not-allowed'"
                            >
                                Gửi lại mã <span v-if="!canResend">({{ cooldown }}s)</span>
                            </button>
                        </p>

                        <a :href="changeRoute" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900 transition duration-200">
                            {{ changeLabel }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
