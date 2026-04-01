<template>
    <div class="relative inline-flex" @mouseenter="show = true" @mouseleave="show = false">
        <slot />
        <transition name="fade">
            <div v-if="show" class="tooltip absolute z-50 bottom-full mb-2
               px-3 py-1.5 text-xs rounded-md whitespace-nowrap
               bg-gray-900 text-white shadow-lg dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                {{ text }}
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
    text: {
        type: String,
        required: true,
    },
})

const show = ref(false)
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 6px;
    border-style: solid;
    border-color: #111827 transparent transparent transparent;
}

.dark .tooltip::after {
    border-color: #616060 transparent transparent transparent;
}
</style>
