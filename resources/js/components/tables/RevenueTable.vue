<!-- components/tables/RevenueTable.vue -->
<template>
    <div class="overflow-hidden">
        <!-- Toggle Tabs -->
        <div class="flex gap-2 mb-4 p-1 bg-gray-100 dark:bg-gray-800 rounded-lg w-fit">
            <button @click="activeTab = 'planned'" :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition-all',
                activeTab === 'planned'
                    ? 'bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
            ]">
                Kế hoạch
            </button>
            <button @click="activeTab = 'actual'" :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition-all',
                activeTab === 'actual'
                    ? 'bg-white dark:bg-gray-700 text-emerald-600 dark:text-emerald-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
            ]">
                Thực tế
            </button>
        </div>


        <!-- Table Container -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="sticky left-0 z-10 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Ngày
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Số đơn
                        </th>
                        <th v-if="activeTab === 'actual'" scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Số hóa đơn
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Tổng doanh thu ({{ defaultCurrencySymbol }})
                        </th>
                       
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Planned Data -->
                    <template v-if="activeTab === 'planned'">
                        <tr v-if="!plannedData || plannedData.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Không có dữ liệu kế hoạch
                            </td>
                        </tr>
                        <tr v-for="(row, index) in sortedPlannedData" :key="'planned-' + index"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td
                                class="sticky left-0 bg-white dark:bg-gray-900 px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                {{ row.date_formatted }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ row.total_orders }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right font-semibold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                {{ formatMoney(row.total_revenue) }}
                            </td>
                            
                        </tr>
                    </template>

                    <!-- Actual Data -->
                    <template v-else>
                        <tr v-if="!actualData || actualData.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Không có dữ liệu thực tế
                            </td>
                        </tr>
                        <tr v-for="(row, index) in sortedActualData" :key="'actual-' + index"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td
                                class="sticky left-0 bg-white dark:bg-gray-900 px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                {{ row.date_formatted }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ row.total_orders }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ row.total_invoices }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ formatMoney(row.total_revenue) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex flex-wrap gap-2">
                                    <div v-for="(rev, idx) in row.revenues_by_currency" :key="'actual-curr-' + idx"
                                        class="inline-flex flex-col px-2.5 py-1.5 rounded-md bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                                {{ formatMoney(rev.amount) }} {{ rev.currency?.symbol ||
                                                rev.currency?.code }}
                                            </span>
                                            <span class="text-xs text-emerald-500 dark:text-emerald-400">({{
                                                rev.invoice_count }})</span>
                                        </div>
                                        <div v-if="rev.amount_converted && shouldShowConverted(rev)"
                                            class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5 font-medium">
                                            ≈ {{ formatMoney(rev.amount_converted) }} {{ defaultCurrencySymbol }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>

                <!-- Summary Footer -->
                <tfoot class="bg-gray-50 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-600">
                    <tr v-if="activeTab === 'planned' && plannedData && plannedData.length > 0">
                        <td
                            class="sticky left-0 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">
                            Tổng
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                            {{ totalPlannedOrders }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-blue-600 dark:text-blue-400">
                            {{ formatMoney(totalPlannedRevenue) }} {{ defaultCurrencySymbol }}
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                    <tr v-if="activeTab === 'actual' && actualData && actualData.length > 0">
                        <td
                            class="sticky left-0 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">
                            Tổng
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                            {{ totalActualOrders }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                            {{ totalActualInvoices }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-emerald-600 dark:text-emerald-400">
                            {{ formatMoney(totalActualRevenue) }} {{ defaultCurrencySymbol }}
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    plannedData: {
        type: Array,
        default: () => []
    },
    actualData: {
        type: Array,
        default: () => []
    },
    defaultCurrencySymbol: {
        type: String,
        default: '₫'
    },
    defaultCurrencyId: {
        type: Number,
        default: 1
    }
})

console.log(">>> props.plannedData", props.plannedData);


const activeTab = ref('planned')

// Format tiền
const formatMoney = (value) => {
    if (!value && value !== 0) return '0'
    return new Intl.NumberFormat('vi-VN').format(Math.round(value))
}

// Kiểm tra có nên hiển thị số tiền quy đổi không
const shouldShowConverted = (revenue) => {
    // Chỉ hiển thị nếu currency khác với default currency
    return revenue.currency?.id !== props.defaultCurrencyId
}

// Sort data by date descending
const sortedPlannedData = computed(() => {
    return [...props.plannedData].sort((a, b) => new Date(b.date) - new Date(a.date))
})

const sortedActualData = computed(() => {
    return [...props.actualData].sort((a, b) => new Date(b.date) - new Date(a.date))
})

// Totals for planned
const totalPlannedOrders = computed(() => {
    return props.plannedData.reduce((sum, row) => sum + row.total_orders, 0)
})

const totalPlannedRevenue = computed(() => {
    return props.plannedData.reduce((sum, row) => sum + row.total_revenue, 0)
})

// Totals for actual
const totalActualOrders = computed(() => {
    return props.actualData.reduce((sum, row) => sum + row.total_orders, 0)
})

const totalActualInvoices = computed(() => {
    return props.actualData.reduce((sum, row) => sum + row.total_invoices, 0)
})

const totalActualRevenue = computed(() => {
    return props.actualData.reduce((sum, row) => sum + row.total_revenue, 0)
})
</script>

<style scoped>
/* Sticky column shadow */
.sticky::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 1px;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.1), transparent);
}
</style>