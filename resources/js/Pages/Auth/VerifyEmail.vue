<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-6 mx-auto">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-blue-100 mb-4">
                    <svg class="h-7 w-7 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Xác minh email</h2>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-100 space-y-6">
                <p class="text-sm text-gray-600 leading-relaxed">
                    Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác minh địa chỉ email bằng cách nhấp vào liên
                    kết chúng tôi vừa gửi cho bạn. Nếu bạn chưa nhận được email, chúng tôi sẽ gửi lại cho bạn.
                </p>

                <!-- Gửi thành công -->
                <div v-if="status === 'verification-link-sent'"
                    class="text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg flex items-center gap-2">
                    <svg class="h-4 w-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    Một liên kết xác minh mới đã được gửi đến địa chỉ email của bạn.
                </div>

                <!-- Gửi lại -->
                <form @submit.prevent="resend">
                    <button type="submit" :disabled="resendForm.processing"
                        class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.01] active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
                        <svg v-if="resendForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>{{ resendForm.processing ? 'Đang gửi...' : 'Gửi lại email xác minh' }}</span>
                    </button>
                </form>

                <!-- Đăng xuất -->
                <div class="text-center">
                    <button type="button"
                        class="text-sm text-gray-500 hover:text-gray-700 underline transition-colors duration-200"
                        @click="logout">
                        Đăng xuất
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({
    status: {
        type: String,
        default: null,
    },
})

const resendForm = useForm({})
const logoutForm = useForm({})

function resend() {
    resendForm.post(route('verification.send'))
}

function logout() {
    logoutForm.get(route('logout'))
}
</script>
