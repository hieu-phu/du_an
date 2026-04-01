<template>
    <Button type="button" variant="secondary" @click="onCancel" :title="title" :class="[
        'px-6 py-3 rounded-xl w-full sm:w-auto flex items-center justify-center dark:text-white',
        extraClass
    ]">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>

        <slot>{{ title }}</slot>
    </Button>
</template>

<script setup>
import Button from '@/components/ui/Button.vue'

/**
 * Props
 */
defineProps({
    /**
     * Custom class truyền thêm từ bên ngoài
     */
    extraClass: {
        type: String,
        default: ''
    },
    title: {
        type: String,
        default: 'Hủy'
    }
})

/**
 * Emits
 */
const emit = defineEmits(['cancel'])

const onCancel = () => {
    emit('cancel')
}
</script>
