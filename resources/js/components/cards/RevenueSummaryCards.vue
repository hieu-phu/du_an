<!-- components/cards/RevenueSummaryCards.vue -->
<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Planned Revenue Card -->
        <div
            class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/30 rounded-xl shadow-lg border border-blue-200 dark:border-blue-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500 dark:bg-blue-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span
                        class="px-3 py-1 bg-blue-200 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold">
                        Kế hoạch
                    </span>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Doanh thu kế hoạch</p>
                    <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                        {{ formatMoney(plannedTotal) }}
                    </p>
                    <p class="text-sm text-blue-600 dark:text-blue-400">
                        {{ currencySymbol }} • {{ plannedOrders }} đơn hàng
                    </p>
                </div>

                <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-blue-600 dark:text-blue-400">Trung bình/đơn</span>
                        <span class="font-semibold text-blue-900 dark:text-blue-100">
                            {{ formatMoney(plannedAverage) }} {{ currencySymbol }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actual Revenue Card -->
        <div
            class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/30 rounded-xl shadow-lg border border-emerald-200 dark:border-emerald-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-500 dark:bg-emerald-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="px-3 py-1 bg-emerald-200 dark:bg-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-semibold">
                        Thực tế
                    </span>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Doanh thu thực tế</p>
                    <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100">
                        {{ formatMoney(actualTotal) }}
                    </p>
                    <p class="text-sm text-emerald-600 dark:text-emerald-400">
                        {{ currencySymbol }} • {{ actualInvoices }} hóa đơn
                    </p>
                </div>

                <div class="mt-4 pt-4 border-t border-emerald-200 dark:border-emerald-700">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-600 dark:text-emerald-400">Trung bình/hóa đơn</span>
                        <span class="font-semibold text-emerald-900 dark:text-emerald-100">
                            {{ formatMoney(actualAverage) }} {{ currencySymbol }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Card -->
        <div :class="[
            'rounded-xl shadow-lg border overflow-hidden',
            difference >= 0
                ? 'bg-gradient-to-br from-emerald-50 to-teal-100 dark:from-emerald-900/20 dark:to-teal-800/30 border-emerald-200 dark:border-emerald-700'
                : 'bg-gradient-to-br from-amber-50 to-orange-100 dark:from-amber-900/20 dark:to-orange-800/30 border-amber-200 dark:border-amber-700'
        ]">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div :class="[
                        'p-3 rounded-lg shadow-md',
                        difference >= 0
                            ? 'bg-emerald-500 dark:bg-emerald-600'
                            : 'bg-amber-500 dark:bg-amber-600'
                    ]">
                        <svg v-if="difference >= 0" class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <svg v-else class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                    <span :class="[
                        'px-3 py-1 rounded-full text-xs font-semibold',
                        difference >= 0
                            ? 'bg-emerald-200 dark:bg-emerald-800 text-emerald-700 dark:text-emerald-300'
                            : 'bg-amber-200 dark:bg-amber-800 text-amber-700 dark:text-amber-300'
                    ]">
                        {{ difference >= 0 ? 'Vượt kế hoạch' : 'Chưa đạt' }}
                    </span>
                </div>

                <div class="space-y-2">
                    <p :class="[
                        'text-sm font-medium',
                        difference >= 0
                            ? 'text-emerald-700 dark:text-emerald-300'
                            : 'text-amber-700 dark:text-amber-300'
                    ]">
                        Chênh lệch
                    </p>
                    <p :class="[
                        'text-3xl font-bold',
                        difference >= 0
                            ? 'text-emerald-900 dark:text-emerald-100'
                            : 'text-amber-900 dark:text-amber-100'
                    ]">
                        {{ difference >= 0 ? '+' : '' }}{{ formatMoney(difference) }}
                    </p>
                    <p :class="[
                        'text-sm',
                        difference >= 0
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : 'text-amber-600 dark:text-amber-400'
                    ]">
                        {{ currencySymbol }} • {{ differencePercent >= 0 ? '+' : '' }}{{ differencePercent }}%
                    </p>
                </div>

                <div :class="[
                    'mt-4 pt-4 border-t',
                    difference >= 0
                        ? 'border-emerald-200 dark:border-emerald-700'
                        : 'border-amber-200 dark:border-amber-700'
                ]">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span
                                :class="difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                                Tỷ lệ đạt được
                            </span>
                            <span :class="[
                                'font-semibold',
                                difference >= 0
                                    ? 'text-emerald-900 dark:text-emerald-100'
                                    : 'text-amber-900 dark:text-amber-100'
                            ]">
                                {{ achievementRate }}%
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div :class="[
                                'h-full rounded-full transition-all duration-500',
                                achievementRate >= 100
                                    ? 'bg-emerald-500 dark:bg-emerald-600'
                                    : 'bg-amber-500 dark:bg-amber-600'
                            ]" :style="{ width: `${Math.min(achievementRate, 100)}%` }"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    plannedData: {
        type: Array,
        default: () => []
    },
    actualData: {
        type: Array,
        default: () => []
    },
    currencySymbol: {
        type: String,
        default: 'VNĐ'
    }
})

const formatMoney = (value) => {
    if (!value && value !== 0) return '0'
    return new Intl.NumberFormat('vi-VN').format(Math.round(value))
}

// Planned totals
const plannedTotal = computed(() => {
    return props.plannedData.reduce((sum, item) => sum + (item.total_revenue || 0), 0)
})

const plannedOrders = computed(() => {
    return props.plannedData.reduce((sum, item) => sum + (item.total_orders || 0), 0)
})

const plannedAverage = computed(() => {
    return plannedOrders.value > 0 ? plannedTotal.value / plannedOrders.value : 0
})

// Actual totals
const actualTotal = computed(() => {
    return props.actualData.reduce((sum, item) => sum + (item.total_revenue || 0), 0)
})

const actualInvoices = computed(() => {
    return props.actualData.reduce((sum, item) => sum + (item.total_invoices || 0), 0)
})

const actualAverage = computed(() => {
    return actualInvoices.value > 0 ? actualTotal.value / actualInvoices.value : 0
})

// Comparison
const difference = computed(() => {
    return actualTotal.value - plannedTotal.value
})

const differencePercent = computed(() => {
    if (plannedTotal.value === 0) return 0
    return ((difference.value / plannedTotal.value) * 100).toFixed(1)
})

const achievementRate = computed(() => {
    if (plannedTotal.value === 0) return 0
    return ((actualTotal.value / plannedTotal.value) * 100).toFixed(1)
})
</script>