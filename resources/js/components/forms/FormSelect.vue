<template>
    <div class="space-y-2">
        <!-- Label -->
        <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            <span class="flex items-center">
                <svg v-if="icon" class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path :d="icon" />
                </svg>
                {{ required ? label : `${label} (nếu có)` }}
                <span v-if="required" class="text-red-500 ml-1">*</span>
            </span>
        </label>

        <!-- Multiselect -->
        <Multiselect :modelValue="modelValue" @update:modelValue="!readonly && $emit('update:modelValue', $event)"
            :options="computedOptions" :searchable="!readonly && searchable" :canClear="!readonly && canClear"
            :disabled="disabled || readonly" :mode="mode" :closeOnSelect="closeOnSelect" :placeholder="placeholder"
            :noOptionsText="noOptionsText" :noResultsText="noResultsText" :limit="limit" :max="max"
            :maxHeight="maxHeight" :loading="loading"
            :delay="serverPagination.enabled ? (delay !== -1 ? delay : 300) : delay" :minChars="minChars"
            :resolveOnLoad="serverPagination.enabled ? true : resolveOnLoad"
            :filterResults="serverPagination.enabled ? false : filterResults" :clearOnSearch="clearOnSearch"
            :clearOnSelect="serverPagination.enabled ? false : clearOnSelect" :object="object" :caret="caret"
            :showOptions="showOptions" :infinite="infinite" @open="handleOpen" @close="handleClose"
            @search-change="handleSearchChange" @select="handleSelect" @deselect="handleDeselect" @clear="handleClear"
            :class="[
                'multiselect-custom px-1 py-1 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500',
                error ? 'multiselect-error' : ''
            ]" v-bind="$attrs">
            <!-- Custom slot khi không tìm thấy kết quả -->
            <template v-if="allowCreate" #noresults>
                <div class="flex items-center justify-between px-3 py-2 hover:bg-gray-50 dark:bg-gray-600 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                    @click="handleAddNew">
                    <span class="text-gray-600 dark:text-gray-400">{{ noResultsText }}</span>
                    <button type="button"
                        class="flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-sm">Thêm mới</span>
                    </button>
                </div>
            </template>

            <!-- Slot hiển thị sau danh sách options - luôn hiển thị nút thêm mới -->
            <template v-if="allowCreate || customButton" #beforelist>
                <div class="border-b border-gray-200 dark:border-gray-700">

                    <div class="flex divide-x divide-gray-200 dark:divide-gray-700">
                        <!-- Nút thêm mới mặc định -->
                        <button v-if="allowCreate" type="button"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                            @click.stop="handleAddNew">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>{{ addNewText }}</span>
                        </button>

                        <!-- Nút tùy biến -->
                        <button v-if="customButton" type="button" :class="[
                            'w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium transition-colors',
                            customButton.color || 'text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300',
                            'hover:bg-gray-50 dark:hover:bg-gray-800'
                        ]" @click.stop="handleCustomButton">
                            <svg v-if="customButton.icon" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    :d="customButton.icon" />
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>{{ customButton.label }}</span>
                        </button>
                    </div>
                </div>
            </template>
        </Multiselect>

        <!-- Helper text -->
        <p v-if="helperText && !error" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ helperText }}
        </p>

        <!-- Error message -->
        <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center">
            {{ error[0] }}
        </p>
    </div>
</template>

<script setup>
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { onMounted, computed, ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number, Array, Object],
        default: null
    },
    options: {
        type: [Array, Function],
        required: true,
        default: () => []
    },
    label: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: 'Chọn một tùy chọn...'
    },
    searchable: {
        type: Boolean,
        default: true
    },
    canClear: {
        type: Boolean,
        default: true
    },
    mode: {
        type: String,
        default: 'single',
        validator: (value) => ['single', 'multiple', 'tags'].includes(value)
    },
    closeOnSelect: {
        type: Boolean,
        default: true
    },
    error: {
        type: String,
        default: ''
    },
    helperText: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    noOptionsText: {
        type: String,
        default: 'Không có tùy chọn'
    },
    noResultsText: {
        type: String,
        default: 'Không tìm thấy kết quả'
    },
    allowCreate: {
        type: Boolean,
        default: false
    },
    addNewText: {
        type: String,
        default: 'Thêm mới'
    },
    // Cấu hình nút tùy biến
    customButton: {
        type: Object,
        default: null,
        // Ví dụ: { label: 'Tạo danh mục mới', icon: 'M5 12h14', color: 'text-purple-600' }
        validator: (value) => {
            if (!value) return true
            return typeof value === 'object' && 'label' in value
        }
    },
    readonly: {
        type: Boolean,
        default: false
    },
    // ===== CẤU HÌNH MỞ RỘNG =====
    // Độ cao tối đa của dropdown (px)
    maxHeight: {
        type: Number,
        default: 160
    },
    // Số lượng option hiển thị ban đầu (pagination)
    limit: {
        type: Number,
        default: -1 // -1 = không giới hạn
    },
    // Số lượng option tối đa có thể chọn (mode: multiple)
    max: {
        type: Number,
        default: -1
    },
    // Loading state
    loading: {
        type: Boolean,
        default: false
    },
    // Delay khi search (ms)
    delay: {
        type: Number,
        default: -1
    },
    // Số ký tự tối thiểu để bắt đầu search
    minChars: {
        type: Number,
        default: 0
    },
    // Load options khi component mount
    resolveOnLoad: {
        type: Boolean,
        default: true
    },
    // Tắt filter local khi dùng server pagination
    autoFilterResults: {
        type: Boolean,
        default: true
    },
    // Filter kết quả
    filterResults: {
        type: Boolean,
        default: true
    },
    // Clear search khi search
    clearOnSearch: {
        type: Boolean,
        default: false
    },
    // Clear search khi select
    clearOnSelect: {
        type: Boolean,
        default: true
    },
    // Options là object {value, label}
    object: {
        type: Boolean,
        default: false
    },
    // Hiển thị icon caret
    caret: {
        type: Boolean,
        default: true
    },
    // Hiển thị options mặc định
    showOptions: {
        type: Boolean,
        default: true
    },
    // Bật infinite scroll
    infinite: {
        type: Boolean,
        default: false
    },
    // Server-side pagination config
    serverPagination: {
        type: Object,
        default: () => ({
            enabled: false,
            url: '',
            perPage: 20,
            currentPage: 1,
            totalPages: 1,
            searchParam: 'search',
            params: {},    // Tham số bổ sung cho request
            cacheResults: true  // Cache kết quả để tránh fetch lại khi select
        })
    },
    // Cấu hình format response từ API
    responseConfig: {
        type: Object,
        default: () => ({
            dataField: 'data',           // Field chứa array data: response.data
            valueField: 'value',         // Field cho value: item.value
            labelField: 'label',         // Field cho label: item.label  
            currentPageField: 'current_page',  // Field trang hiện tại
            lastPageField: 'last_page',        // Field tổng số trang
            totalField: 'total',               // Field tổng số items
            // Custom transform function (optional)
            transform: null              // (response) => array of options
        })
    },
})

const emit = defineEmits([
    'update:modelValue',
    'add-new',
    'custom-action', // Event mới cho nút tùy biến
    'open',
    'close',
    'search-change',
    'select',
    'deselect',
    'clear'
])

// State cho server pagination
const serverOptions = ref([])
const isLoadingMore = ref(false)
const currentPage = ref(1)
const hasMorePages = ref(true)
const searchQuery = ref('')
const lastFetchedQuery = ref(null) // Track query đã fetch để tránh duplicate

// Computed options - kết hợp local và server options
const computedOptions = computed(() => {
    if (props.serverPagination.enabled) {
        return async (query) => {
            // Xử lý query null/undefined khi component mount
            const searchValue = query === null || query === undefined ? '' : query

            // Nếu query giống lần fetch trước và đã có data → return cache
            if (lastFetchedQuery.value === searchValue && serverOptions.value.length > 0) {
                return serverOptions.value
            }
            lastFetchedQuery.value = searchValue
            searchQuery.value = searchValue
            currentPage.value = 1

            return await fetchServerOptions(searchValue, 1)
        }
    }

    return typeof props.options === 'function' ? props.options : props.options
})

// Fetch options từ server
const fetchServerOptions = async (query = '', page = 1) => {
    try {
        const { url, perPage, searchParam } = props.serverPagination
        const config = props.responseConfig

        // Xử lý query null/undefined - set về empty string
        const searchValue = query === null || query === undefined ? '' : query

        const params = new URLSearchParams({
            [searchParam]: searchValue,
            page: page,
            per_page: perPage,
            ...props.serverPagination.params
        })

        const response = await fetch(`${url}?${params}`)
        const data = await response.json()

        // Xử lý response với custom transform function
        let newOptions = []

        if (config.transform && typeof config.transform === 'function') {
            // Sử dụng custom transform function
            newOptions = config.transform(data)
        } else {
            // Lấy data từ field được config (fallback: data.data || data.items || data)
            const rawData = data[config.dataField] || data.items || data.data || data

            // Transform data theo valueField và labelField
            newOptions = Array.isArray(rawData) ? rawData.map(item => {
                // Nếu item đã đúng format {value, label}, giữ nguyên
                if (typeof item === 'object' && item.value !== undefined && item.label !== undefined) {
                    return item
                }

                // Transform theo config
                return {
                    value: item[config.valueField] ?? item.id ?? item.value,
                    label: item[config.labelField] ?? item.name ?? item.label ?? item.text
                }
            }) : []
        }

        if (page === 1) {
            serverOptions.value = newOptions
        } else {
            serverOptions.value = [...serverOptions.value, ...newOptions]
        }

        // Cập nhật pagination info với field names được config
        const currentPageValue = data[config.currentPageField] ?? data.current_page ?? page
        const lastPageValue = data[config.lastPageField] ?? data.last_page ?? 1

        hasMorePages.value = currentPageValue < lastPageValue
        currentPage.value = page

        return serverOptions.value
    } catch (error) {
        console.error('Error fetching server options:', error)
        return []
    }
}

// Load more khi scroll
const loadMore = async () => {
    if (!hasMorePages.value || isLoadingMore.value) return

    isLoadingMore.value = true
    await fetchServerOptions(searchQuery.value, currentPage.value + 1)
    isLoadingMore.value = false
}

// Event handlers
const handleAddNew = () => {
    emit('add-new')
}

const handleCustomButton = () => {
    emit('custom-action')
}

const handleOpen = () => {
    emit('open')
}

const handleClose = () => {
    emit('close')
}

const handleSearchChange = (query) => {
    emit('search-change', query)
}

const handleSelect = (value) => {
    emit('select', value)
}

const handleDeselect = (value) => {
    emit('deselect', value)
}

const handleClear = () => {
    // Reset cache khi clear
    if (props.serverPagination.enabled) {
        lastFetchedQuery.value = null
        serverOptions.value = []
    }
    emit('clear')
}

onMounted(() => {
    let listOptions = document.querySelectorAll('.multiselect-custom .multiselect-options');
    if (listOptions.length > 0) {
        listOptions.forEach(option => {
            option.classList.add('dark:bg-gray-950');
        });
    }

    // Setup infinite scroll nếu được bật
    if (props.infinite && props.serverPagination.enabled) {
        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting) {
                    loadMore()
                }
            },
            { threshold: 1.0 }
        )

        // Observe dropdown list
        setTimeout(() => {
            const dropdown = document.querySelector('.multiselect-custom .multiselect-dropdown')
            if (dropdown) {
                observer.observe(dropdown)
            }
        }, 100)
    }
})
</script>

<style>
/* Multiselect custom styles */
.multiselect-custom {
    --ms-ring-width: 1.5px;
    --ms-ring-color: #3b82f6;
    --ms-option-bg-pointed: #eff6ff;
    --ms-option-bg-selected: #3b82f6;
    --ms-option-bg-selected-pointed: #2563eb;
    --ms-option-color-pointed: #1e40af;
    --ms-radius: 0.25rem;
    --ms-bg: #ffffff;
    --ms-border-color: #d1d5db;
    --ms-text-color: #111827;
    --ms-placeholder-color: #6b7280;
}

/* Dark mode - theo theme của ứng dụng */
.dark .multiselect-custom {
    --ms-bg: #1f2937;
    --ms-border-color: #374151;
    --ms-text-color: #e5e7eb;
    --ms-placeholder-color: #9ca3af;
    --ms-option-bg: #1f2937;
    --ms-option-bg-pointed: #374151;
    --ms-option-bg-selected: #3b82f6;
    --ms-option-bg-selected-pointed: #2563eb;
    --ms-option-color: #e5e7eb;
    --ms-option-color-pointed: #ffffff;
    --ms-option-color-selected: #ffffff;
    --ms-clear-color: #9ca3af;
    --ms-clear-color-hover: #ef4444;
    --ms-spinner-color: #3b82f6;
}

.dark .multiselect-custom .multiselect-options {
    background: #111827;
}

/* Light mode */
.multiselect-error {
    border-color: #ef4444 !important;
}

/* Dark mode */
.dark .dark\:multiselect-error {
    border-color: #ef4444 !important;
}

/* Custom scrollbar cho dropdown */
:deep(.multiselect-dropdown) {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

:deep(.multiselect-dropdown::-webkit-scrollbar) {
    width: 8px;
}

:deep(.multiselect-dropdown::-webkit-scrollbar-track) {
    background: transparent;
}

:deep(.multiselect-dropdown::-webkit-scrollbar-thumb) {
    background-color: #cbd5e1;
    border-radius: 4px;
}

.dark :deep(.multiselect-dropdown::-webkit-scrollbar-thumb) {
    background-color: #475569;
}

/* Loading indicator */
:deep(.multiselect-spinner) {
    border-color: #3b82f6 transparent transparent transparent;
}
</style>