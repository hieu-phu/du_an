<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 mx-auto">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-blue-100 mb-4">
                    <svg class="h-7 w-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Cập nhật số điện thoại</h2>
                <p class="mt-2 text-sm text-gray-600">Hoàn tất hồ sơ của bạn để tiếp tục</p>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-gray-100 space-y-6">
                <!-- Flash messages -->
                <div v-if="flash.success" class="rounded-lg bg-green-50 p-4 flex items-start gap-3">
                    <svg class="h-5 w-5 text-green-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ flash.success }}</p>
                </div>
                <div v-if="flash.warning" class="rounded-lg bg-yellow-50 p-4 flex items-start gap-3">
                    <svg class="h-5 w-5 text-yellow-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-yellow-800">{{ flash.warning }}</p>
                </div>
                <div v-if="flash.error" class="rounded-lg bg-red-50 p-4 flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ flash.error }}</p>
                </div>

                <!-- Google account info -->
                <div v-if="user.provider === 'google'" class="rounded-lg bg-blue-50 p-4 flex items-center gap-3">
                    <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                    </svg>
                    <p class="text-sm text-blue-700">
                        Tài khoản Google: <span class="font-medium">{{ user.email }}</span>
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <!-- Số điện thoại -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input id="phone" v-model="form.phone" type="tel" autocomplete="tel" autofocus required
                            pattern="[0-9]{10,11}" title="Số điện thoại phải có 10-11 chữ số"
                            class="block w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400"
                            :class="form.errors.phone ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                            placeholder="Nhập số điện thoại (10-11 số)" />
                        <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</p>
                        <p class="mt-1 text-xs text-gray-500">Ví dụ: 0912345678</p>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit" :disabled="form.processing"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-[1.01] disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>{{ form.processing ? 'Đang cập nhật...' : 'Cập nhật số điện thoại' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const flash = computed(() => usePage().props.flash ?? {})

const form = useForm({
    phone: props.user.phone ?? '',
})

function submit() {
    form.put(route('phone.update'))
}
</script>
