<template>
    <Button type="submit" variant="primary" :disabled="disabled || loading" :title="submitText" :class="[
        'px-6 py-3 rounded-xl w-full sm:w-auto flex items-center justify-center gap-2',
        gradientClass,
        extraClass
    ]">
        <!-- Loading -->
        <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>

        <!-- Success icon -->
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>

        <!-- Label -->
        <span>
            <slot>{{ submitText }}</slot>
        </span>
    </Button>
</template>

<script setup>
import { computed } from 'vue'
import Button from '@/components/ui/Button.vue'

/**
 * Props
 */
const props = defineProps({
    loading: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    isEditMode: {
        type: Boolean,
        default: false
    },
    /**
     * Cho phép custom class ngoài
     */
    extraClass: {
        type: String,
        default: ''
    }
})

/**
 * Text hiển thị
 */
const submitText = computed(() =>
    props.isEditMode ? 'Cập nhật' : 'Lưu'
)

/**
 * Gradient có thể override sau này
 */
const gradientClass =
    'bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg hover:shadow-xl transition-shadow'
</script>
