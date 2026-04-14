<template>
  <div
    class="config-table bg-white dark:bg-gray-800 shadow-[0 5px 10px rgba(151, 164, 175, .05)] dark:shadow-[0 5px 10px rgba(0, 0, 0, 0.3)] border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto overflow-y-auto">
      <table class="min-w-full w-full table-auto border-collapse">
        <!-- Header luôn cố định -->
        <thead
          class="sticky top-0 z-10 border-b border-gray-100 dark:border-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800">
          <tr>
            <th v-if="showIndex" class="px-4 py-3 text-center whitespace-nowrap">
              #
            </th>
            <th v-for="col in columns" :key="col.key" class="px-4 py-3 whitespace-nowrap uppercase"
              :title="col.title || col.label"
              :class="col.align === 'text-right' ? 'text-right' : col.align === 'text-center' ? 'text-center' : 'text-left'">
              {{ col.label }}
            </th>
            <th v-if="hasVisibleActions" class="px-4 py-3 text-center whitespace-nowrap">
              THAO TÁC
            </th>
          </tr>
        </thead>

        <!-- Loading State -->
        <tbody v-if="loading" class="bg-white dark:bg-gray-800">
          <tr>
            <td :colspan="columns.length + (showIndex ? 1 : 0) + (hasVisibleActions ? 1 : 0)"
              class="px-4 py-16 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <!-- Spinner -->
                <div class="relative w-12 h-12">
                  <div class="absolute inset-0 border-4 border-gray-200 dark:border-gray-700 rounded-full"></div>
                  <div
                    class="absolute inset-0 border-4 border-blue-600 dark:border-blue-500 rounded-full border-t-transparent animate-spin">
                  </div>
                </div>
                <!-- Loading text -->
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                  Đang tải dữ liệu...
                </p>
              </div>
            </td>
          </tr>
        </tbody>

        <!-- Empty State -->
        <tbody v-else-if="!tableData.length" class="bg-white dark:bg-gray-800">
          <tr>
            <td :colspan="totalColumns" class="px-4 py-12 text-center">
              <div class="text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center gap-3">
                  <img src="/images/icons/data-empty-placeholder.png" alt="No data"
                    class="w-20 h-20 object-contain opacity-80 dark:opacity-70" />
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ emptyMessage }}
                  </p>
                </div>
              </div>
            </td>
          </tr>
        </tbody>

        <!-- Data Rows -->
        <tbody v-else class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700 text-sm">
          <tr v-for="(item, index) in tableData" :key="item.id || index"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 cursor-pointer">
            <!-- Index -->
            <td v-if="showIndex" class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 text-center font-medium">
              {{ indexOffset + index + 1 }}
            </td>

            <!-- Data columns -->
            <td v-for="col in columns" :key="col.key" :title="col.title || col.label"
              class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300"
              :class="[col.class, col.align === 'text-right' ? 'text-right' : col.align === 'text-center' ? 'text-center' : 'text-left']">
              <slot :name="`cell-${col.key}`" :item="item">
                {{ getCellValue(item, col.key, col.defaultValue) }}
              </slot>
            </td>

            <!-- Actions -->
            <td v-if="hasVisibleActionsForItem(item)"
              class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 text-center">
              <div class="flex justify-center gap-2">
                <template v-for="(action, idx) in actions" :key="idx">
                  <template v-if="!shouldHideAction(action, item)">
                    <component v-if="action.component" :is="action.component"
                      v-bind="typeof action.componentProps === 'function' ? action.componentProps(item) : action.componentProps"
                      class="inline-block">
                      <template v-for="menuItem in typeof action.slot === 'function' ? action.slot(item) : action.slot">
                        <!-- Divider -->
                        <div v-if="menuItem.type === 'divider'"
                          class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                        <button v-else type="button" v-bind="menuItem.props || {}" @click.stop="menuItem.onClick?.()"
                          class="flex w-full items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                          :title="menuItem.title || ''">
                          <component :is="menuItem.icon" class="w-4 h-4" v-if="menuItem.icon" />
                          <span>{{ menuItem.label }}</span>
                        </button>
                      </template>
                    </component>

                    <Tooltip v-else-if="action.buttonProps?.title" :text="action.buttonProps.title">
                      <button type="button" v-bind="filteredButtonProps(action.buttonProps)"
                        @click="action.onClick?.(item)"
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <component :is="typeof action.icon === 'function' ? action.icon(item) : action.icon"
                          class="w-5 h-5" v-if="action.icon" />
                        <span v-if="action.label" class="ml-1 text-xs">{{ action.label }}</span>
                      </button>
                    </Tooltip>

                    <button v-else type="button" v-bind="action.buttonProps || {}" @click="action.onClick?.(item)"
                      :title="action.buttonProps?.title || ''"
                      class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                      <component :is="typeof action.icon === 'function' ? action.icon(item) : action.icon"
                        class="w-5 h-5" v-if="action.icon" />
                      <span v-if="action.label" class="ml-1 text-xs">{{ action.label }}</span>
                    </button>
                  </template>
                </template>
              </div>
            </td>
          </tr>
        </tbody>

        <!-- Summary Footer -->
        <tfoot v-if="(summaries.length && !loading && tableData.length) || $slots.paginator"
          class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700 text-sm">
          <tr v-if="summaries.length && !loading && tableData.length" class="bg-gray-100 dark:bg-gray-900">
            <!-- Index -->
            <td v-if="showIndex" class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 text-center uppercase">
              {{ props.titleSummaries }}
            </td>

            <!-- Data columns -->
            <td v-for="col in columns" :key="col.key" class="px-4 py-3 font-bold text-gray-900 dark:text-gray-200"
              :class="summaryMap[col.key]?.align || col.align || 'text-right'">
              <template v-if="summaryMap[col.key]">
                {{
                  summaryMap[col.key].format
                    ? summaryMap[col.key].format(
                      summaryValues[summaryMap[col.key].key]
                    )
                    : summaryValues[summaryMap[col.key].key]
                }}
                <span v-if="summaryUnits[summaryMap[col.key].key]"
                  class="ml-1 text-sm text-gray-900 dark:text-gray-200">
                  {{ summaryUnits[summaryMap[col.key].key] }}
                </span>
              </template>
            </td>

            <!-- Actions -->
            <td v-if="hasVisibleActions"></td>
          </tr>

          <tr class="border-t border-gray-100 dark:border-gray-700">
            <td :colspan="totalColumns" class="px-4 py-3 bg-white dark:bg-gray-800">
              <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Paginator info -->
                <div class="text-sm text-gray-600 dark:text-gray-400">
                  <slot name="paginator-info">
                    <!-- Default slot content -->
                  </slot>
                </div>

                <!-- Paginator controls -->
                <div class="flex items-center gap-3">
                  <slot name="paginator" />
                </div>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Skeleton from 'primevue/skeleton'
import Tooltip from '@/components/ui/Tooltip.vue'

const props = defineProps({
  columns: { type: Array, required: true },
  data: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  showIndex: { type: Boolean, default: false },
  indexOffset: { type: Number, default: 0 },
  actions: { type: Array, default: () => [] },
  emptyMessage: { type: String, default: 'Không có dữ liệu' },
  titleSummaries: {
    type: String,
    default: "TỔNG"
  },
  summaries: {
    type: Array,
    default: () => []
  }
})

const shouldHideAction = (action, item) => {
  if (typeof action.hidden === 'function') {
    return action.hidden(item);
  }
  return !!action.hidden;
}

// Kiểm tra xem có action nào visible cho một item cụ thể không
const hasVisibleActionsForItem = (item) => {
  return props.actions.some(action => !shouldHideAction(action, item))
}

// Kiểm tra xem có bất kỳ action nào visible trong toàn bộ table không
const hasVisibleActions = computed(() => {
  // Nếu không có actions thì return false
  if (!props.actions.length) return false

  // Nếu có data, kiểm tra xem có ít nhất 1 item có visible action không
  if (props.data.length) {
    return props.data.some(item => hasVisibleActionsForItem(item))
  }

  // Nếu không có data, kiểm tra xem có action nào không phải luôn hidden không
  return props.actions.some(action => {
    // Nếu hidden là function, ta không thể biết chắc nên giữ cột
    if (typeof action.hidden === 'function') return true
    // Nếu hidden là boolean và = false thì action này visible
    return !action.hidden
  })
})

const tableData = computed(() => props.data)

const filteredButtonProps = (props) => {
  if (!props) return {}
  const { title, ...rest } = props
  return rest
}

const totalColumns = computed(() => {
  let count = props.columns.length
  if (props.showIndex) count++
  if (hasVisibleActions.value) count++
  return count
})

const getCellValue = (item, key, defaultValue = '') => {
  if (!key) return defaultValue
  const keys = key.split('.')
  let value = item
  for (const k of keys) {
    value = value?.[k]
    if (value === undefined || value === null) return defaultValue
  }
  return value
}

const summaryValues = computed(() => {
  const result = {}
  props.summaries.forEach(summary => {
    result[summary.key] = props.data.reduce((sum, item) => {
      if (typeof summary.compute === 'function') {
        const value = summary.compute(item)
        return sum + (Number(value) || 0)
      }
      const value = getCellValue(item, summary.key, 0)
      return sum + (Number(value) || 0)
    }, 0)
  })
  return result
})

const summaryMap = computed(() => {
  const map = {}
  props.summaries.forEach(summary => {
    const cellKey = summary.cell || summary.key
    map[cellKey] = summary
  })
  return map
})

const summaryUnits = computed(() => {
  const result = {}
  props.summaries.forEach(summary => {
    if (summary.unitFrom && props.data.length) {
      result[summary.key] = getCellValue(
        props.data[0],
        summary.unitFrom,
        ''
      )
    } else if (summary.unit) {
      result[summary.key] = summary.unit;
    }
  })
  return result
})
</script>


<style scoped>
.config-table {
  border-radius: 0.75rem;
}

.datatable-wrapper {
  position: relative;
}

/* Shimmer Skeleton Effect */
.shimmer-skeleton {
  position: relative;
  overflow: hidden;
  background-color: #e5e7eb !important;
  border-radius: inherit;
}

.dark .shimmer-skeleton {
  background-color: #374151 !important;
}

.shimmer-skeleton::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg,
      transparent 0%,
      rgba(255, 255, 255, 0.4) 50%,
      transparent 100%);
  transform: translateX(-100%);
  animation: shimmer 1.8s infinite linear;
}

.dark .shimmer-skeleton::after {
  background: linear-gradient(90deg,
      transparent 0%,
      rgba(255, 255, 255, 0.15) 50%,
      transparent 100%);
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background-color: #f3f4f6;
  border-radius: 4px;
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
  background-color: #1f2937;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #9ca3af;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #4b5563;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #6b7280;
}

/* Dark mode for divider in actions */
.dark .border-gray-200 {
  border-color: #374151;
}
</style>
