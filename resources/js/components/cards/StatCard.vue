<template>
    <div :class="cardClass" class="rounded-xl p-6 border transition-all hover:shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                    {{ title }}
                </p>
                <p :class="valueClass" class="text-2xl font-bold">
                    {{ value }} <span class="text-lg font-normal">{{ currency }}</span>
                </p>
            </div>
            <!-- <div :class="iconBgClass" class="w-12 h-12 rounded-lg flex items-center justify-center">
                <component :is="icon" :class="iconClass" class="w-6 h-6" />
            </div> -->
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import TrendingUpIcon from '@/icons/EyeOff.vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    currency: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'orange', 'purple', 'red'].includes(value)
    },
    icon: {
        type: Object,
        default: () => TrendingUpIcon
    }
})

const colorClasses = {
    blue: {
        card: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
        value: 'text-blue-700 dark:text-blue-400',
        iconBg: 'bg-blue-100 dark:bg-blue-900/40',
        icon: 'text-blue-600 dark:text-blue-400'
    },
    green: {
        card: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
        value: 'text-green-700 dark:text-green-400',
        iconBg: 'bg-green-100 dark:bg-green-900/40',
        icon: 'text-green-600 dark:text-green-400'
    },
    orange: {
        card: 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800',
        value: 'text-orange-700 dark:text-orange-400',
        iconBg: 'bg-orange-100 dark:bg-orange-900/40',
        icon: 'text-orange-600 dark:text-orange-400'
    },
    purple: {
        card: 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800',
        value: 'text-purple-700 dark:text-purple-400',
        iconBg: 'bg-purple-100 dark:bg-purple-900/40',
        icon: 'text-purple-600 dark:text-purple-400'
    },
    red: {
        card: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
        value: 'text-red-700 dark:text-red-400',
        iconBg: 'bg-red-100 dark:bg-red-900/40',
        icon: 'text-red-600 dark:text-red-400'
    }
}

const cardClass = computed(() => colorClasses[props.color].card)
const valueClass = computed(() => colorClasses[props.color].value)
const iconBgClass = computed(() => colorClasses[props.color].iconBg)
const iconClass = computed(() => colorClasses[props.color].icon)
</script>